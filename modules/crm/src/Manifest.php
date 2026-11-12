<?php

namespace Moduloom\Crm;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'crm';
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
            ['slug' => 'crm', 'label' => 'CRM', 'sort_weight' => 50],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'crm.manage', 'label' => 'Manage CRM contacts'],
        ];
    }
}
