<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Phase 6a: a single account gate, not yet a role system. Registration
 * is only reachable while no User exists at all — it bootstraps the
 * one owner account a fresh instance needs, then closes itself.
 * Distinguishing Owner/Staff/Customer logins is Phase 6b's job, once
 * user_role_assignments exists.
 */
class AuthController extends Controller
{
    public function showRegister()
    {
        if (User::query()->exists()) {
            return redirect('/login');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (User::query()->exists()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create($validated);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/setup');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Those credentials don\'t match an account.',
            ]);
        }

        $request->session()->regenerate();

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
