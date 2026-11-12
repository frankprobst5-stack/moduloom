<?php

namespace App\Kernel;

/**
 * A profile ("ecosystem" in MASTER_VISION.md's language) is a
 * meta-package: a named, versioned bundle of modules plus the default
 * role grants and starter theme that go with it. The kernel never
 * stores a profile's definition — only the fact that one was applied
 * (profile_install_history) and the referrers it left behind
 * (module_references, role_capability_grants).
 */
interface ProfileManifest
{
    public function slug(): string;

    public function label(): string;

    public function version(): string;

    /**
     * Module slugs this profile installs. Each is installed through
     * ModuleInstaller, which resolves that module's own dependencies
     * and runs its migrations — a profile never provisions a module's
     * schema directly.
     *
     * @return string[]
     */
    public function modules(): array;

    /**
     * Suggested, not auto-installed — surfaced to the user as optional
     * add-ons. Phase 2 does not need any profile to populate this.
     *
     * @return string[]
     */
    public function optionalModules(): array;

    /**
     * Capability grants this profile attaches to roles it creates or
     * finds already present (invariant 3) — never redefines a role.
     *
     * @return array<int, array{role_slug: string, role_label: string, capability_slug: string, capability_label: string}>
     */
    public function roleGrants(): array;

    /**
     * A starter theme identifier. Real theme *application* is an OPEN
     * question in MASTER_ARCHITECTURE.md — for now this is just a
     * label the onboarding screen can display.
     */
    public function themeSlug(): string;
}
