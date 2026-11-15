<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Registration is only reachable while no User exists at all — it
 * bootstraps the one owner account a fresh instance needs, then closes
 * itself. There is no sign-up flow beyond this; Staff/Customer accounts
 * (Phase 6c) get created by an Owner, not self-registered here.
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

        // The 'owner' role_slug is shared across every ecosystem profile
        // (invariant 3) — whichever side needs it first creates it.
        // Nothing has installed a profile yet at registration time, so
        // this is that first need; later profile installs only ever
        // attach capability grants to this same role, never redefine it.
        $owner = Role::firstOrCreate(['role_slug' => 'owner'], ['label' => 'Owner']);
        $user->roles()->syncWithoutDetaching([$owner->role_slug]);

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
