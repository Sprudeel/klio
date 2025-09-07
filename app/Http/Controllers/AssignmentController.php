<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /*
     * Show a specific assignment.
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $submissions = $assignment->submissions()
            ->latest('submitted_at')
            ->paginate(20);

        return view('assignments.show', compact('assignment', 'submissions'));
    }

    /*
     * Show the last 20 assignments.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');

        $assignments = Assignment::query()
            ->where('author', Auth::id())
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%"))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('assignments.index', compact('assignments', 'q'));
    }

    public function indexAll(Request $request)
    {
        $q = $request->get('q');

        $assignments = Assignment::query()
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('assignments.index', compact('assignments', 'q'));
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
            'is_closed'   => ['sometimes', 'boolean'],
        ]);

        $assignment->update($data);

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Aufgabe aktualisiert.');
    }

    /** Close assignment (lock new submissions) */
    public function close(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $assignment->update([
            'is_closed' => true,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Aufgabe geschlossen.');
    }

    /** Open assignment (allow submissions) */
    public function open(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $assignment->update([
            'is_closed' => false,
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
