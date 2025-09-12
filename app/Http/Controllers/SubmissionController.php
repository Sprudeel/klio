<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use iio\libmergepdf\Merger;

class SubmissionController extends Controller
{

    /** List submissions for an assignment (author only) */
    public function index(Assignment $assignment, Request $request)
    {
        $this->authorize('view', $assignment);

        $q = $request->get('q');

        $subs = $assignment->submissions()
            ->when($q, fn($qq) => $qq->where(function ($x) use ($q) {
                $x->where('student_name', 'like', "%{$q}%")
                    ->orWhere('student_email', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('original_filename', 'like', "%{$q}%");
            }))
            ->latest('submitted_at')
            ->paginate(20)->withQueryString();

        return view('submissions.index', compact('assignment', 'subs', 'q'));
    }

    /** Public form to submit */
    public function create(Request $request)
    {
        if($request->code != "") {
            $assignment = Assignment::query()->where('code', 'like', "%$request->code%")->firstOrFail();
        } else {
            $assignment = null;
        }

        return view('submissions.create', compact('assignment'));
    }

    /** Store (private disk) */
    public function store(Request $request)
    {
        $assignment = Assignment::query()->where('code', '=', $request->code)->firstOrFail();

        abort_if($assignment->isClosed, 403, 'Abgabe geschlossen.');

        $data = $request->validate([
            'student_name'  => ['required', 'string', 'max:255'],
            'code'          => ['required', 'string', 'max:255'],
            'file'          => ['required','file','mimes:pdf','mimetypes:application/pdf','max:20480'],
        ]);

        $file = $request->file('file');

        if (! $this->looksLikePdf($file->getRealPath())) {
            return back()->withErrors(['file' => 'Die Datei ist kein gültiges PDF.']);
        }

        $path = $file->store("submissions/{$assignment->code}", 'private');

        $submission = $assignment->submissions()->create([
            'student_name'      => $data['student_name'] ,
            'original_filename' => $file->getClientOriginalName(),
            'storage_path'      => $path,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'checksum'          => hash_file('sha256', $file->getRealPath()),
            'submitted_at'      => now(),
        ]);

        return redirect()
            ->route('assignments.submitted',
                [
                    'assignment' => $assignment->name,
                    'author' => $assignment->author()->first()->name,
                    'student_name' => $request->student_name,
                    'file_name' => $file->getClientOriginalName(),
                    'submitted_at' => $submission->submitted_at,
                ]);
    }

    public function submitted(Request $request) {
        return view('submissions.submitted', compact('request'));
    }

    /** Tiny header check for PDF magic bytes. */
    private function looksLikePdf(string $path): bool
    {
        $fh = @fopen($path, 'rb');
        if (!$fh) return false;
        $bytes = fread($fh, 5);
        fclose($fh);
        return $bytes === '%PDF-';
    }

    /** Show one submission (details) */
    public function show(Submission $submission)
    {
        // GET assignment
        $this->authorize('view', $submission);
        return view('submissions.show', compact('submission'));
    }

    /** Download (auth gated, private disk) */
    public function download(Submission $submission)
    {
        $this->authorize('view', $submission);

        $disk = Storage::disk('private');

        if (!$disk->exists($submission->storage_path)) {
            abort(404);
        }

        $safeName = preg_replace('/[^\w\-. ]+/', '_', $submission->original_filename ?: 'download');
        return $disk->download($submission->storage_path, $safeName, [
            'Content-Type' => $submission->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function merge(\App\Models\Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $subs = $assignment->submissions()->orderBy('submitted_at')->get();
        if ($subs->isEmpty()) {
            return back()->with('error', 'Keine Abgaben vorhanden.');
        }

        $disk = Storage::disk('private');
        $merger = new Merger();

        foreach ($subs as $sub) {
            if (!$sub->storage_path || ! $disk->exists($sub->storage_path)) {
                continue;
            }

            // Final sanity check: skip if not pdf
            $ext = strtolower(pathinfo($sub->storage_path, PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                continue;
            }

            $merger->addFile($disk->path($sub->storage_path));
        }

        try {
            $blob = $merger->merge(); // raw PDF
        } catch (\Throwable $e) {
            return back()->with('error', 'PDF-Zusammenführung fehlgeschlagen.');
        }

        $safeName = 'klio_' . $assignment->code . '_abgaben.pdf';

        return response()->streamDownload(
            fn() => print($blob),
            $safeName,
            ['Content-Type' => 'application/pdf']
        );
    }

    /** Delete a submission */
    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);

        // delete file then soft delete row
        $disk = Storage::disk('private');
        if ($submission->storage_path && $disk->exists($submission->storage_path)) {
            $disk->delete($submission->storage_path);
        }

        $submission->delete();

        return back()->with('success', 'Abgabe gelöscht.');
    }

    public function edit(Assignment $assignment, Submission $submission)
    {
        // Ensure this submission belongs to the assignment (string FK by code)
        abort_unless($submission->code === $assignment->code, 404);

        // Eager-load the relation used in policies/views
        $submission->loadMissing('assignment');

        $this->authorize('update', $submission);

        return view('submissions.edit', compact('assignment', 'submission'));
    }

    public function update(Request $request, Assignment $assignment, Submission $submission)
    {
        abort_unless($submission->code === $assignment->code, 404);

        $this->authorize('update', $submission);

        $data = $request->validate([
            'student_name' => ['required','string','max:255'],
            // File is optional during edit; if present it must be a PDF up to 20 MB
            'file'         => ['nullable','file','mimes:pdf','mimetypes:application/pdf','max:20480'],
        ]);

        // If a new file is provided, replace the old one
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Optional: keep your extra magic-bytes check
            if (! $this->looksLikePdf($file->getRealPath())) {
                return back()->withErrors(['file' => 'Die Datei ist kein gültiges PDF.'])->withInput();
            }

            // delete old if exists
            $disk = Storage::disk('private');
            if ($submission->storage_path && $disk->exists($submission->storage_path)) {
                $disk->delete($submission->storage_path);
            }

            $newPath = $file->store("submissions/{$assignment->code}", 'private');

            // Update file-related columns
            $submission->original_filename = $file->getClientOriginalName();
            $submission->storage_path      = $newPath;
            $submission->file_size         = $file->getSize();
            $submission->mime_type         = $file->getMimeType();
            $submission->checksum          = hash_file('sha256', $file->getRealPath());
            // keep submitted_at as-is (it’s the original submission moment)
        }

        // Update meta fields
        $submission->student_name = $data['student_name'];

        $submission->save();

        return redirect()
            ->route('assignments.show', $assignment)
            ->with('success', 'Abgabe aktualisiert.');
    }
}
