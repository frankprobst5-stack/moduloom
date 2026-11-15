<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Phase 6a: a single account gate in front of every route. These tests
 * exercise the gate itself — register/login/logout and what happens
 * without a session — not the application behavior behind it, which is
 * what every other feature test (now authenticated by default via
 * TestCase::setUp()) already covers.
 */
class AuthTest extends TestCase
{
    public function test_registering_the_first_account_logs_it_in_and_sends_it_to_setup(): void
    {
        User::query()->delete();
        Auth::logout();

        $response = $this->post('/register', [
            'name' => 'Frank',
            'email' => 'frank@example.com',
            'password' => 'correct-horse',
        ]);

        $response->assertRedirect('/setup');
        $this->assertTrue(Auth::check());
        $this->assertDatabaseHas('users', ['email' => 'frank@example.com']);
    }

    public function test_registration_closes_itself_once_an_account_already_exists(): void
    {
        Auth::logout();

        $this->post('/register', [
            'name' => 'Second Owner',
            'email' => 'second@example.com',
            'password' => 'correct-horse',
        ])->assertNotFound();

        $this->get('/register')->assertRedirect('/login');
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        Auth::logout();
        $user = User::factory()->create(['password' => 'correct-horse']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-horse',
        ])->assertRedirect('/');

        $this->assertTrue(Auth::check());
    }

    public function test_login_fails_with_the_wrong_password(): void
    {
        Auth::logout();
        $user = User::factory()->create(['password' => 'correct-horse']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertFalse(Auth::check());
    }

    public function test_logout_ends_the_session(): void
    {
        $this->post('/logout')->assertRedirect('/login');

        $this->assertFalse(Auth::check());
    }

    public function test_unauthenticated_api_requests_are_rejected(): void
    {
        Auth::logout();

        $this->getJson('/api/dashboard/nav')->assertUnauthorized();
    }

    public function test_unauthenticated_admin_pages_redirect_to_login(): void
    {
        Auth::logout();

        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/marketplace')->assertRedirect('/login');
        $this->get('/setup')->assertRedirect('/login');
    }
}
