<?php

namespace Tests;

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

        // Phase 6a: every /api/* route now requires an authenticated
        // session. The kernel/module/profile tests in this suite are
        // about what the application does once someone is logged in,
        // not about the login gate itself — so authenticate a user by
        // default here rather than repeating actingAs() in every one of
        // them. Tests\Feature\Auth\AuthTest exercises the gate directly
        // and opts out of this per test.
        $this->actingAs(User::factory()->create());
    }
}
