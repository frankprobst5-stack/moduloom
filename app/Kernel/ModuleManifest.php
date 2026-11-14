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

    /**
     * A short, end-user-facing one-liner for the module marketplace —
     * what it does, not how it's built. Distinct from composer.json's
     * own `description`, which is written for a developer reading the
     * package listing, not a user browsing a marketplace card.
     */
    public function description(): string;

    /**
     * Groups this module in the marketplace (Content, Commerce,
     * Community, Education, Enterprise, Government, Productivity).
     * Display grouping only — the kernel never filters or gates
     * anything by category.
     */
    public function category(): string;
}
