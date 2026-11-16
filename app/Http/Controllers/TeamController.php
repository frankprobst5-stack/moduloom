<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Owner-only (capability:instance.manage): create the other accounts
 * this instance needs and see who already has one. The Owner sets the
 * password directly — there's no real outbound mail in this app
 * (MAIL_MAILER=log) to do an invite-link flow, and a self-hosted
 * single-admin app handing a new teammate their own credentials
 * directly is a normal, honest way to bootstrap one.
 */
class TeamController extends Controller
{
    public function index()
    {
        return [
            'members' => User::with('roles')->get()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('role_slug'),
            ]),
            'available_roles' => Role::all(['role_slug', 'label']),
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_slug' => ['required', 'string', 'exists:roles,role_slug'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->roles()->attach($validated['role_slug']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => [$validated['role_slug']],
        ], 201);
    }
}
