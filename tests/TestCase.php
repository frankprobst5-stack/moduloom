<?php

namespace Tests;

use App\Kernel\Reconciler;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    /**
     * Deliberately NOT RefreshDatabase. Its per-test transaction
     * rollback silently breaks the moment a test installs a module
     * for the first time: ModuleInstaller runs that module's real
     * migration, CREATE TABLE is DDL, and MariaDB commits any open
     * transaction the instant DDL runs — the same root cause as the
     * Phase 2 ProfileInstaller transaction bug, this time breaking the
     * test framework's own isolation instead of application code.
     * Confirmed by finding module tables and kernel rows still present
     * in the test database after a full suite run that should have
     * left it empty. A real migrate:fresh before every test is slower
     * but actually isolates tests from each other.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', ['--force' => true]);

        // Phase 6a/6b: every /api/* route now requires an authenticated
        // session holding the capability a route needs. The
        // kernel/module/profile tests in this suite are about what the
        // application does once someone is logged in as its owner, not
        // about the auth gate itself — so authenticate an Owner by
        // default here, the same way AuthController::register bootstraps
        // one, rather than repeating this in every one of them.
        // Tests\Feature\Auth\* exercise the gates directly and opt out
        // of this per test.
        $user = User::factory()->create();
        app(Reconciler::class)->grantCapability('owner', 'Owner', 'instance.manage', 'Manage the instance', 'kernel');
        $user->roles()->syncWithoutDetaching(['owner']);
        $this->actingAs($user);
    }
}
