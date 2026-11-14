<?php

namespace Tests\Support;

use App\Kernel\ModuleManifest;

/**
 * A fake module that depends on `notes`, used only to exercise the
 * module_dependency reference type through the real ModuleInstaller —
 * the same proof Phase 0 did directly against the Reconciler, now one
 * layer up through a real manifest.
 */
class FakeDependentManifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'fake-dependent';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function requires(): array
    {
        return ['notes'];
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/empty-migrations';
    }

    public function navEntries(): array
    {
        return [];
    }

    public function capabilities(): array
    {
        return [];
    }

    public function description(): string
    {
        return 'Fake, test-only module.';
    }

    public function category(): string
    {
        return 'Test';
    }
}
