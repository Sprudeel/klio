<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    /*
     * Show a specific assignment.
     */
    public function show(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

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
        return view('assignments.create');
    }

    /*
     * Store new assignment
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:32', 'unique:assignments,code'],
            'deadline'    => ['nullable', 'date'],
            'color'       => ['nullable', 'string', 'max:32'],
            'icon'        => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
        ]);

        $data['author'] = auth()->id();
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
        $this->authorize('update', $assignment);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

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
        $this->authorize('update', $assignment);

        $assignment->update([
            'isClosed' => true,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Aufgabe geschlossen.');
    }

    /** Open assignment (allow submissions) */
    public function open(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $assignment->update([
            'isClosed' => false,
            'closed_at' => null,
        ]);

        return back()->with('success', 'Aufgabe wieder geöffnet.');
    }

    /** Delete (cascades will remove submissions if set) */
    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Aufgabe gelöscht.');
    }

}
