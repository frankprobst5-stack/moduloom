<?php

namespace App\Kernel;

use App\Models\Capability;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

/**
 * Sits above the Reconciler: resolves a module's manifest, installs its
 * declared dependencies first, and — only the first time a module is
 * ever created — runs its migrations. Re-installing an already-present
 * module (a second referrer showing up) never touches its schema again.
 */
class ModuleInstaller
{
    public function __construct(private readonly Reconciler $reconciler) {}

    public function manifestFor(string $moduleSlug): ModuleManifest
    {
        $registry = config('modules', []);

        if (! isset($registry[$moduleSlug])) {
            throw new RuntimeException("No module manifest registered for slug [{$moduleSlug}].");
        }

        return App::make($registry[$moduleSlug]);
    }

    public function install(string $moduleSlug, string $referenceType, string $referenceIdentifier): void
    {
        $manifest = $this->manifestFor($moduleSlug);

        foreach ($manifest->requires() as $requiredSlug) {
            $this->install($requiredSlug, 'module_dependency', $moduleSlug);
        }

        $module = $this->reconciler->installModule(
            $moduleSlug,
            $referenceType,
            $referenceIdentifier,
            $manifest->version(),
        );

        if ($module->wasRecentlyCreated) {
            Artisan::call('migrate', [
                '--path' => $this->relativeMigrationsPath($manifest->migrationsPath()),
                '--force' => true,
            ]);
        }

        foreach ($manifest->capabilities() as $capability) {
            // Module-contributed capabilities exist, but Phase 1 doesn't
            // grant them to any role yet — that's a profile's job
            // (invariant 3), and there are no profiles until Phase 2.
            Capability::firstOrCreate(
                ['capability_slug' => $capability['slug']],
                ['label' => $capability['label']],
            );
        }
    }

    public function uninstall(string $moduleSlug, string $referenceType, string $referenceIdentifier): void
    {
        // Deliberately does not touch migrations or data. Removal only
        // ever orphans — see MASTER_ARCHITECTURE.md's non-destructive
        // removal rule.
        $this->reconciler->removeReference($moduleSlug, $referenceType, $referenceIdentifier);
    }

    private function relativeMigrationsPath(string $absolutePath): string
    {
        return str_replace(base_path().'/', '', $absolutePath);
    }
}
