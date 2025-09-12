<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AssignmentController extends Controller
{
    /*
     * Show a specific assignment.
     */
    public function show(Request $request, $assignment)
    {
        $assignment = Assignment::where('id', $assignment)
            ->orWhere('code', $assignment)
            ->firstOrFail();

        Gate::authorize('view', [$assignment]);

        $query = $assignment->submissions()->latest();

        if ($q = $request->string('q')->toString()) {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('filename', 'like', "%{$q}%");
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        switch ($request->string('sort','latest')) {
            case 'oldest': $query->oldest(); break;
            case 'name':   $query->orderBy('name'); break;
            default:       $query->latest(); break;
        }

        $submissions = $query->paginate(12);

        // Eager count for header
        $assignment->loadCount('submissions');

        return view('assignments.show', compact('assignment', 'submissions'));
    }

    /*
     * Show the last 20 assignments.
     */
    public function index(Request $request)
    {
        Gate::authorize('index', Assignment::class);

        $q    = $request->string('q')->toString();
        $due  = $request->string('due')->toString();
        $sort = $request->string('sort', 'deadline_asc')->toString();

        $assignments = Assignment::query()
            ->where('author_id', $request->user()->id)
            ->when($q, fn($qry) => $qry->where(function($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            }))
            ->when($due === 'today', fn($qry) => $qry->whereBetween('deadline', [now()->startOfDay(), now()->endOfDay()]))
            ->when($due === 'week', fn($qry) => $qry->whereBetween('deadline', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($due === 'overdue', fn($qry) => $qry->where('deadline', '<', now()))
            ->when($sort === 'deadline_asc',  fn($qry) => $qry->orderBy('deadline', 'asc'))
            ->when($sort === 'deadline_desc', fn($qry) => $qry->orderBy('deadline', 'desc'))
            ->when($sort === 'created_desc',  fn($qry) => $qry->orderBy('id', 'desc'))
            ->when($sort === 'created_asc',   fn($qry) => $qry->orderBy('id', 'asc'))
            ->withCount('submissions')
            ->paginate(12);

        return view('assignments.index', compact('assignments'), ['all' => false]);
    }

    public function indexAll(Request $request)
    {
        Gate::authorize('viewAny', Assignment::class);

        $q = $request->get('q');

        $assignments = Assignment::query()
            ->latest('created_at')
            ->with(['author:id,name'])
            ->paginate(15)
            ->withQueryString();

        return view('assignments.index', compact('assignments', 'q'), ['all' => true]);
    }


    /*
     * View for creating assignments.
     */
    public function create()
    {
        Gate::authorize('create', Assignment::class);

        return view('assignments.create');
    }

    /*
     * Store new assignment
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Assignment::class);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:32', 'unique:assignments,code'],
            'deadline'    => ['nullable', 'date'],
            'color'       => ['nullable', 'string', 'max:32'],
            'icon'        => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
        ]);

        $data['author_id'] = auth()->id();
        $data['code'] = $data['code'] ?? Str::upper(Str::random(8));

        $assignment = Assignment::create($data);

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Aufgabe erstellt.');
    }

    /*
     * Edit an assignment.
     */
    public function edit(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['required', 'string', 'max:32', 'unique:assignments,code,' . $assignment->id],
            'deadline'    => ['nullable', 'date'],
            'color'       => ['nullable', 'string', 'max:32'],
            'icon'        => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
        ]);

        $originalCode = $assignment->getOriginal('code');

        DB::transaction(function () use ($assignment, $data, $originalCode) {
            $assignment->update($data);

            if (isset($data['code']) && $data['code'] !== $originalCode) {
                    if (Schema::hasColumn('submissions', 'code')) {
                        DB::table('submissions')
                            ->where('code', $originalCode)
                            ->update(['code' => $data['code']]);
                    }
            }
        });

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Aufgabe aktualisiert.');
    }

    /** Close assignment (lock new submissions) */
    public function close(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $assignment->update([
            'isClosed' => true,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Aufgabe geschlossen.');
    }

    /** Open assignment (allow submissions) */
    public function open(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $assignment->update([
            'isClosed' => false,
            'closed_at' => null,
        ]);

        return back()->with('success', 'Aufgabe wieder geöffnet.');
    }

    /** Delete (cascades will remove submissions if set) */
    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Aufgabe gelöscht.');
    }

    /** Download all submissions as a ZIP */
    public function zip(Assignment $assignment)
    {
        // Allow only author/admin (adjust policy name if you use a different one)
        Gate::authorize('export', $assignment);

        $submissions = $assignment->submissions()->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Keine Einreichungen zum Exportieren vorhanden.');
        }

        $zip = new ZipArchive();
        $filename = sprintf(
            'klio-%s-%s.zip',
            Str::slug($assignment->name ?: 'aufgabe'),
            $assignment->code
        );

        $tmp = tempnam(sys_get_temp_dir(), 'klio_zip_');
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'ZIP konnte nicht erstellt werden.');
        }

        $disk = Storage::disk('private');

        foreach ($submissions as $s) {
            if (!$s->storage_path || !$disk->exists($s->storage_path)) {
                continue;
            }

            $abs = $disk->path($s->storage_path);

            // Example name inside the zip: STUDENT - original_filename.pdf
            $zipName = trim(
                implode(' - ', array_filter([
                    $s->student_name,
                    $s->original_filename ?: ('submission-'.$s->id.'.pdf'),
                ]))
            );

            // Ensure unique name inside the zip
            $base = $zipName;
            $i = 1;
            while ($zip->locateName($zipName) !== false) {
                $zipName = pathinfo($base, PATHINFO_FILENAME) . " ($i)." . pathinfo($base, PATHINFO_EXTENSION);
                $i++;
            }

            $zip->addFile($abs, $zipName);
        }

        $zip->close();

        // Stream to user and delete temp after send
        return response()->download($tmp, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /** Merge all submissions into a single PDF (order: newest last) */
    public function merge(Assignment $assignment)
    {
        Gate::authorize('export', $assignment);

        $submissions = $assignment->submissions()
            ->orderBy('created_at') // oldest -> newest
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Keine Einreichungen zum Zusammenführen vorhanden.');
        }

        $disk = Storage::disk('private');

        // Use libmergepdf
        $merger = new Merger();

        $added = 0;
        foreach ($submissions as $s) {
            if (!$s->storage_path || !$disk->exists($s->storage_path)) {
                continue;
            }
            // (Optional) skip non-PDFs – should already be PDFs in your app
            $mime = $s->mime_type ?: 'application/pdf';
            if ($mime !== 'application/pdf') {
                continue;
            }

            $merger->addFile($disk->path($s->storage_path));
            $added++;
        }

        if ($added === 0) {
            return back()->with('error', 'Es konnten keine gültigen PDF-Dateien gefunden werden.');
        }

        $mergedBinary = $merger->merge();

        $downloadName = sprintf(
            'klio-%s-%s-merged.pdf',
            Str::slug($assignment->name ?: 'aufgabe'),
            $assignment->code
        );

        return response($mergedBinary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }
}
