<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

        $updatesPath = resource_path('data/updates.json');
        $updates = [];
        if (File::exists($updatesPath)) {
            try {
                $decoded = json_decode(File::get($updatesPath), true, 512, JSON_THROW_ON_ERROR);
                $updates = collect($decoded)
                    ->map(function ($u) {
                        return [
                            'type'  => Arr::get($u, 'type', 'misc'),
                            'title' => Arr::get($u, 'title', ''),
                            'desc'  => Arr::get($u, 'desc', ''),
                            'tag'   => Arr::get($u, 'tag', null),
                            'date'  => Arr::get($u, 'date', null),
                        ];
                    })
                    ->sortByDesc(fn($u) => $u['date'] ?? '0000-00-00')
                    ->values()
                    ->all();
            } catch (\Throwable $e) {
                $updates = [];
            }
        }

        // Optional: your $build array if you use it in the view
        $build = [
            'version'    => config('app.version') ?? 'dev',
            'commit'     => config('app.commit_short') ?? null,
            'source_url' => config('app.source_url') ?? null,
            'env'        => app()->environment(),
        ];

        return view('dashboard', compact('user', 'assignments', 'recentSubs', 'updates', 'build'));
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
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
