<?php

namespace Moduloom\Records;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'records';
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
            ['slug' => 'records', 'label' => 'Records', 'sort_weight' => 210],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'records.manage', 'label' => 'Manage public records'],
        ];
    }
}
