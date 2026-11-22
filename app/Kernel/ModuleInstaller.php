<?php

namespace App\Kernel;

use App\Models\InstalledModule;
use App\Models\ModuleReference;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

/**
 * Sits above the Reconciler: resolves a module's manifest, installs its
 * declared dependencies first, and runs its migrations through
 * Laravel's own migrator every time install() is called — not just on
 * first create. That's what makes a module's schema upgradeable at
 * all: Laravel's migration repository already tracks which specific
 * migration files have run, per path, so calling `migrate --path=...`
 * again is a safe no-op for files it's already seen and picks up any
 * new one a module ships later. A later profile referencing an
 * already-installed module never re-provisions its *data* (invariant
 * 2) — this only ever adds schema the module's own manifest declares,
 * the same thing first-install already did.
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

        $this->reconciler->installModule(
            $moduleSlug,
            $referenceType,
            $referenceIdentifier,
            $manifest->version(),
        );

        Artisan::call('migrate', [
            '--path' => $this->relativeMigrationsPath($manifest->migrationsPath()),
            '--force' => true,
        ]);

        foreach ($manifest->capabilities() as $capability) {
            // Phase 1 deferred granting these to anyone — there were no
            // profiles yet to do it. Phase 6b makes that gap real: a
            // profile's own roleGrants() cover the capabilities it cares
            // about, but a module installed standalone (the marketplace,
            // or a direct ModuleInstaller call) was never granted to
            // anyone at all, Owner included. Every module's own owner
            // capability is granted here, unconditionally, as its
            // baseline — "you installed it, you can manage it" — with a
            // distinct source per invariant 3, so a profile's own grant
            // for the same capability coexists rather than conflicts.
            $this->reconciler->grantCapability(
                'owner',
                'Owner',
                $capability['slug'],
                $capability['label'],
                'module:'.$moduleSlug,
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

    /**
     * The "separate, explicitly-confirmed, distinct action"
     * MASTER_ARCHITECTURE.md's non-destructive removal rule always
     * said purging would have to be — never reachable from uninstall()
     * itself. Only ever allowed on a module that is actually orphaned:
     * refusing an active or disabled module here is what keeps this
     * from ever being used as a shortcut around the reference-count
     * rule invariant 1 exists to enforce.
     *
     * Reverses every migration this module's own path has ever run,
     * regardless of which batch it ran in (--step with a number far
     * larger than any real module will ever have), then releases the
     * module-sourced capability grant (install() granted one;
     * invariant 3 means any profile's own separate grant for the same
     * capability is untouched and survives) and removes the kernel's
     * own record that this module ever existed.
     */
    public function purge(string $moduleSlug): void
    {
        $module = InstalledModule::where('module_slug', $moduleSlug)->first();

        if (! $module || $module->status !== 'orphaned') {
            throw new RuntimeException(
                "Cannot purge [{$moduleSlug}]: it must be orphaned (zero referrers) first.",
            );
        }

        $manifest = $this->manifestFor($moduleSlug);

        Artisan::call('migrate:rollback', [
            '--path' => $this->relativeMigrationsPath($manifest->migrationsPath()),
            '--step' => 9999,
            '--force' => true,
        ]);

        foreach ($manifest->capabilities() as $capability) {
            $this->reconciler->revokeGrant('owner', $capability['slug'], 'module:'.$moduleSlug);
        }

        ModuleReference::where('module_slug', $moduleSlug)->delete();
        $module->delete();
    }

    private function relativeMigrationsPath(string $absolutePath): string
    {
        return str_replace(base_path().'/', '', $absolutePath);
    }
}
