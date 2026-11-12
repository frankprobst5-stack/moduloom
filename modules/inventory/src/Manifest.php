<?php

namespace Moduloom\Inventory;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'inventory';
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
            ['slug' => 'inventory', 'label' => 'Inventory', 'sort_weight' => 60],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'inventory.manage', 'label' => 'Manage inventory'],
        ];
    }
}
