<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Dashboard (e.g., my assignments & recent submissions) */
    public function dashboard()
    {
        $user = Auth::user();

        $assignments = $user->assignments()
            ->latest()
            ->limit(6)
            ->get();

        $recentSubs = $user->assignments()
            ->with(['submissions' => fn($q) => $q->latest('submitted_at')->limit(5)])
            ->get()
            ->sortByDesc('submitted_at');

        return view('dashboard', compact('user', 'assignments', 'recentSubs'));
    }

    /** Profile view */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /** Update profile */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil aktualisiert.');
    }

    /** Change password */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Aktuelles Passwort ist falsch.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Passwort geändert.');
    }
}
