<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function createPublic(Assignment $assignment)
    {
        abort_if($assignment->is_closed || ($assignment->deadline && now()->greaterThan($assignment->deadline)), 403, 'Abgabe geschlossen.');
        return view('submissions.create', compact('assignment'));
    }

    /** Store (private disk) */
    public function store(Request $request, Assignment $assignment)
    {
        abort_if($assignment->is_closed, 403, 'Abgabe geschlossen.');

        $data = $request->validate([
            'student_name'  => ['nullable', 'string', 'max:255'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'file'          => ['required','file','mimes:pdf','mimetypes:application/pdf','max:20480'],
        ]);

        $file = $request->file('file');

        if (! $this->looksLikePdf($file->getRealPath())) {
            return back()->withErrors(['file' => 'Die Datei ist kein gültiges PDF.']);
        }

        $path = $file->store("submissions/{$assignment->id}", 'private');

        $submission = $assignment->submissions()->create([
            'student_name'      => $data['student_name'] ?? auth()->user()->name ?? null,
            'original_filename' => $file->getClientOriginalName(),
            'storage_path'      => $path,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(), // should be application/pdf
            'checksum'          => hash_file('sha256', $file->getRealPath()),
            'submitted_at'      => now(),
        ]);

        return redirect()
            ->route('assignments.show', $assignment)
            ->with('success', "Abgabe gespeichert ({$submission->code}).");
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

    public function mergedPdf(\App\Models\Assignment $assignment)
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
}
