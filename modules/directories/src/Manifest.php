<?php

namespace Moduloom\Directories;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'directories';
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
            ['slug' => 'directories', 'label' => 'Directories', 'sort_weight' => 110],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'directories.manage', 'label' => 'Manage directory listings'],
        ];
    }

    public function description(): string
    {
        return 'A searchable directory of listings.';
    }

    public function category(): string
    {
        return 'Community';
    }
}
