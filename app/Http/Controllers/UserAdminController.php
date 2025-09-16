<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function ensureAdmin(): void
    {
        abort_unless(Auth::user() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin(), 403, 'Nur für Admins');
    }

    /** List users */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $q = trim((string) $request->get('q', ''));
        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    /** Show create form */
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.users.create');
    }

    /** Store new user */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'is_admin' => ['sometimes','boolean'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            // if you store admin flag as boolean column:
            ...(array_key_exists('is_admin', $data) ? ['is_admin' => (bool) $data['is_admin']] : []),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Benutzer erstellt.');
    }

    /** Show reset password form for any user */
    public function editPassword(User $user)
    {
        $this->ensureAdmin();
        return view('admin.users.edit_password', compact('user'));
    }

    /** Update (reset) password for any user */
    public function updatePassword(Request $request, User $user)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        // If the admin resets their own password, keep them logged in
        if ($user->id === Auth::id()) {
            Auth::login($user);
        }

        return redirect()->route('admin.users.index')->with('success', 'Passwort aktualisiert.');
    }
}
