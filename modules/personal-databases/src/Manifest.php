<?php

namespace Moduloom\PersonalDatabases;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'personal-databases';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function requires(): array
    {
        return [];
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/../database/migrations';
    }

    public function navEntries(): array
    {
        return [
            ['slug' => 'personal-databases', 'label' => 'Databases', 'sort_weight' => 40],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'personal_databases.manage', 'label' => 'Manage personal databases'],
        ];
    }
}
