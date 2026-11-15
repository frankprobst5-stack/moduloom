<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * The root URL must never show Laravel's generic scaffold page, and
 * since Phase 6a it has to route through the account gate too: no
 * account yet goes to registration, an account with no active session
 * goes to login, and only from an authenticated session does "fresh
 * profile vs. installed profile" apply.
 */
class RootRouteTest extends TestCase
{
    public function test_a_totally_fresh_instance_redirects_to_register(): void
    {
        // TestCase::setUp() authenticates a default user for every test
        // in this suite — this is the one scenario that needs there to
        // be no account at all, so undo that here.
        User::query()->delete();
        Auth::logout();

        $this->get('/')->assertRedirect('/register');
    }

    public function test_an_account_with_no_active_session_redirects_to_login(): void
    {
        Auth::logout();

        $this->get('/')->assertRedirect('/login');
    }

    public function test_a_logged_in_user_with_nothing_installed_redirects_to_setup(): void
    {
        $this->get('/')->assertRedirect('/setup');
    }

    public function test_a_logged_in_user_with_something_installed_redirects_to_the_dashboard(): void
    {
        app(ProfileInstaller::class)->install('personal', 'owner');

        $this->get('/')->assertRedirect('/dashboard');
    }
}
