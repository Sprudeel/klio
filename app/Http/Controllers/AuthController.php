<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard')->with('success', 'Registered successfully');
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = ['email' => $request->email, 'password' => $request->password];

        $user = User::where('email', $request->email)->first();

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->route('dashboard', ['user' => $user])->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
            'password' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('welcome')->with('success', 'Your are logged out!');
    }



    public function profile($user)
    {
        if (Auth::id() != $user) abort(403, 'unauthorized');
        $user = User::find(Auth::id());
        return view('user.profile', compact('user'));
    }
    public function change_password(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return Redirect()->back()->with('error', 'Incorect Password');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('profile', ['user' => $user])->with('success', 'Password changed Successfully!');
    }

    public function reset_password(Request $request): \Illuminate\Http\RedirectResponse
    {
        $result =  DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$result) {
            return redirect()->back()->with('error', 'INVALID');
        }
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        $user = User::where('email', $request->email)->first();

        $user->update([
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);

        Auth::logoutOtherDevices();

        return redirect()->route('profile', ['user' => $user])->with('success', 'Password Reset successfully!');
    }

}
