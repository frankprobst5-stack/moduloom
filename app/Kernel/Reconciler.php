<?php

namespace App\Kernel;

use App\Models\Capability;
use App\Models\InstalledModule;
use App\Models\ModuleReference;
use App\Models\ProfileInstallHistory;
use App\Models\Role;
use App\Models\RoleCapabilityGrant;
use Illuminate\Support\Facades\DB;

/**
 * Implements the four kernel invariants from MASTER_ARCHITECTURE.md.
 *
 * This class is the only thing in the codebase allowed to write to
 * installed_modules, module_references, roles, capabilities, and
 * role_capability_grants. Nothing else should touch those tables
 * directly — that's what keeps the invariants actually enforced
 * instead of merely documented.
 */
class Reconciler
{
    /**
     * Invariant 1 + 2: records a referrer for a module. Creates the
     * module (status active) only if it does not already exist — an
     * existing module's own provisioning is never re-touched. The
     * reference row is written every time, idempotently, regardless
     * of whether the module already existed.
     */
    public function installModule(
        string $moduleSlug,
        string $referenceType,
        string $referenceIdentifier,
        string $version = '0.0.0',
    ): InstalledModule {
        return DB::transaction(function () use ($moduleSlug, $referenceType, $referenceIdentifier, $version) {
            $module = InstalledModule::firstOrCreate(
                ['module_slug' => $moduleSlug],
                ['version' => $version, 'status' => 'active', 'installed_at' => now()],
            );

            // A module that lost all its referrers and was orphaned, then
            // gains a new referrer, becomes active again — its data was
            // never touched while orphaned, so reactivating is safe.
            if ($module->status === 'orphaned') {
                $module->update(['status' => 'active']);
            }

            ModuleReference::firstOrCreate([
                'module_slug' => $moduleSlug,
                'reference_type' => $referenceType,
                'reference_identifier' => $referenceIdentifier,
            ]);

            return $module->refresh();
        });
    }

    /**
     * Invariant 1: removes one referrer. The module is never deleted
     * here — it is only orphaned once zero distinct referrers remain.
     */
    public function removeReference(string $moduleSlug, string $referenceType, string $referenceIdentifier): void
    {
        DB::transaction(function () use ($moduleSlug, $referenceType, $referenceIdentifier) {
            ModuleReference::where('module_slug', $moduleSlug)
                ->where('reference_type', $referenceType)
                ->where('reference_identifier', $referenceIdentifier)
                ->delete();

            if ($this->referenceCount($moduleSlug) === 0) {
                InstalledModule::where('module_slug', $moduleSlug)->update(['status' => 'orphaned']);
            }
        });
    }

    public function referenceCount(string $moduleSlug): int
    {
        return ModuleReference::where('module_slug', $moduleSlug)->count();
    }

    public function isInstalled(string $moduleSlug): bool
    {
        return InstalledModule::where('module_slug', $moduleSlug)
            ->whereIn('status', ['active', 'disabled'])
            ->exists();
    }

    /**
     * Invariant 3: attaches a capability grant to a role. Creates the
     * role/capability only if missing — an existing role is extended,
     * never redefined.
     */
    public function grantCapability(
        string $roleSlug,
        string $roleLabel,
        string $capabilitySlug,
        string $capabilityLabel,
        string $grantedBySource,
    ): RoleCapabilityGrant {
        return DB::transaction(function () use ($roleSlug, $roleLabel, $capabilitySlug, $capabilityLabel, $grantedBySource) {
            Role::firstOrCreate(['role_slug' => $roleSlug], ['label' => $roleLabel]);
            Capability::firstOrCreate(['capability_slug' => $capabilitySlug], ['label' => $capabilityLabel]);

            return RoleCapabilityGrant::firstOrCreate([
                'role_slug' => $roleSlug,
                'capability_slug' => $capabilitySlug,
                'granted_by_source' => $grantedBySource,
            ]);
        });
    }

    /**
     * Invariant 3: revokes only the grant from this specific source.
     * The capability remains on the role if any other source still
     * grants the same (role, capability) pair — no extra bookkeeping
     * needed, the unique constraint already guarantees each source
     * has at most one row, so this is a correct N:1 check by
     * construction.
     */
    public function revokeGrant(string $roleSlug, string $capabilitySlug, string $grantedBySource): void
    {
        RoleCapabilityGrant::where('role_slug', $roleSlug)
            ->where('capability_slug', $capabilitySlug)
            ->where('granted_by_source', $grantedBySource)
            ->delete();
    }

    public function roleHasCapability(string $roleSlug, string $capabilitySlug): bool
    {
        return RoleCapabilityGrant::where('role_slug', $roleSlug)
            ->where('capability_slug', $capabilitySlug)
            ->exists();
    }

    /**
     * Invariant 4: applies a profile as one imperative, point-in-time
     * transition. Safe to call more than once for the same profile —
     * module installs and capability grants are idempotent; only the
     * history row is appended every time, by design, as an audit trail.
     *
     * @param  array<int, array{module_slug: string, version?: string}>  $modules
     * @param  array<int, array{role_slug: string, role_label: string, capability_slug: string, capability_label: string}>  $roleGrants
     */
    public function applyProfile(
        string $profileSlug,
        string $version,
        array $modules,
        array $roleGrants = [],
        ?string $installedBy = null,
    ): void {
        DB::transaction(function () use ($profileSlug, $version, $modules, $roleGrants, $installedBy) {
            foreach ($modules as $module) {
                $this->installModule(
                    $module['module_slug'],
                    'profile',
                    $profileSlug,
                    $module['version'] ?? '0.0.0',
                );
            }

            foreach ($roleGrants as $grant) {
                $this->grantCapability(
                    $grant['role_slug'],
                    $grant['role_label'],
                    $grant['capability_slug'],
                    $grant['capability_label'],
                    $profileSlug,
                );
            }

            ProfileInstallHistory::create([
                'profile_slug' => $profileSlug,
                'version' => $version,
                'installed_by' => $installedBy,
                'installed_at' => now(),
            ]);
        });
    }

    /**
     * Removes every reference and grant this profile introduced.
     * Modules and capability grants another profile still needs are
     * left untouched — this is the non-destructive removal the whole
     * project exists to prove.
     */
    public function removeProfile(string $profileSlug): void
    {
        DB::transaction(function () use ($profileSlug) {
            $moduleSlugs = ModuleReference::where('reference_type', 'profile')
                ->where('reference_identifier', $profileSlug)
                ->pluck('module_slug');

            foreach ($moduleSlugs as $moduleSlug) {
                $this->removeReference($moduleSlug, 'profile', $profileSlug);
            }

            RoleCapabilityGrant::where('granted_by_source', $profileSlug)->delete();
        });
    }
}
