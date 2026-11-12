<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

/**
 * The smallest ecosystem — chosen as Moduloom's first real profile
 * specifically because its modules have no cross-module business
 * dependencies on each other, so this proves the profile mechanism
 * itself without the module-dependency complexity Phase 3 adds.
 */
class PersonalProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'personal';
    }

    public function label(): string
    {
        return 'Personal';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        return ['website', 'notes', 'files', 'tasks', 'personal-databases'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        $grants = [
            ['capability_slug' => 'website.manage', 'capability_label' => 'Manage website pages'],
            ['capability_slug' => 'notes.manage', 'capability_label' => 'Manage notes'],
            ['capability_slug' => 'tasks.manage', 'capability_label' => 'Manage tasks'],
            ['capability_slug' => 'files.manage', 'capability_label' => 'Manage files'],
            ['capability_slug' => 'personal_databases.manage', 'capability_label' => 'Manage personal databases'],
        ];

        return array_map(
            fn (array $grant) => ['role_slug' => 'owner', 'role_label' => 'Owner', ...$grant],
            $grants,
        );
    }

    public function themeSlug(): string
    {
        return 'personal-default';
    }
}
