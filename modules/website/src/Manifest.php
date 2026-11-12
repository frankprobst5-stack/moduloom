<?php

namespace Moduloom\Website;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'website';
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
            ['slug' => 'website', 'label' => 'Website', 'sort_weight' => 0],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'website.manage', 'label' => 'Manage website pages'],
        ];
    }
}
