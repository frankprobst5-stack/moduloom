<?php

namespace App\Kernel;

/**
 * Every module package implements this. The kernel never introspects a
 * module's internals beyond what this contract exposes — slug, version,
 * dependencies, migrations, and what it contributes to navigation and
 * the permission model.
 */
interface ModuleManifest
{
    public function slug(): string;

    public function version(): string;

    /**
     * Other module slugs this module requires. Feeds the
     * `module_dependency` reference type — installing this module
     * installs each of these first, recorded as dependency referrers.
     *
     * @return string[]
     */
    public function requires(): array;

    /**
     * Absolute path to this module's own Laravel migrations. Run only
     * by ModuleInstaller, only on a module's first install — never by
     * a blanket `artisan migrate`, and never again once the module
     * already exists (invariant 2: never re-provisioned).
     */
    public function migrationsPath(): string;

    /**
     * @return array<int, array{slug: string, label: string, sort_weight: int}>
     */
    public function navEntries(): array;

    /**
     * @return array<int, array{slug: string, label: string}>
     */
    public function capabilities(): array;
}
