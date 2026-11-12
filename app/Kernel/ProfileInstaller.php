<?php

namespace App\Kernel;

use App\Models\ProfileInstallHistory;
use Illuminate\Support\Facades\App;
use RuntimeException;

/**
 * Sits above ModuleInstaller the same way ModuleInstaller sits above
 * Reconciler. Applying a profile installs every module it declares
 * (each through ModuleInstaller, so dependency resolution and
 * first-install migrations still happen correctly), attaches its role
 * grants, and records one point-in-time history row (invariant 4).
 *
 * Safe to call more than once for the same profile: every module
 * install and capability grant underneath is idempotent. Only the
 * history row is appended every time, by design — it is an audit
 * trail, never consulted to decide current state.
 */
class ProfileInstaller
{
    public function __construct(
        private readonly ModuleInstaller $moduleInstaller,
        private readonly Reconciler $reconciler,
    ) {}

    public function manifestFor(string $profileSlug): ProfileManifest
    {
        $registry = config('profiles', []);

        if (! isset($registry[$profileSlug])) {
            throw new RuntimeException("No profile manifest registered for slug [{$profileSlug}].");
        }

        return App::make($registry[$profileSlug]);
    }

    public function install(string $profileSlug, ?string $installedBy = null): ProfileManifest
    {
        $manifest = $this->manifestFor($profileSlug);

        // Deliberately NOT one big DB::transaction() wrapping all of
        // this. A module's first install can run real migrations, and
        // CREATE TABLE is DDL — MariaDB/MySQL commits an open
        // transaction implicitly the moment DDL runs. Wrapping that in
        // an outer transaction doesn't make it atomic, it just breaks
        // the outer transaction once the implicit commit fires. Each
        // step below is already safely idempotent on its own
        // (Reconciler wraps its own bookkeeping writes), which is what
        // actually matters for invariant 4.
        foreach ($manifest->modules() as $moduleSlug) {
            $this->moduleInstaller->install($moduleSlug, 'profile', $manifest->slug());
        }

        foreach ($manifest->roleGrants() as $grant) {
            $this->reconciler->grantCapability(
                $grant['role_slug'],
                $grant['role_label'],
                $grant['capability_slug'],
                $grant['capability_label'],
                $manifest->slug(),
            );
        }

        ProfileInstallHistory::create([
            'profile_slug' => $manifest->slug(),
            'version' => $manifest->version(),
            'installed_by' => $installedBy,
            'installed_at' => now(),
        ]);

        return $manifest;
    }
}
