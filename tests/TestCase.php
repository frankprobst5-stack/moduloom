<?php

namespace Tests;

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
    }
}
