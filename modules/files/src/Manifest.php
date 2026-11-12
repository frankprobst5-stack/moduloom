<?php

namespace Moduloom\Files;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'files';
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
            ['slug' => 'files', 'label' => 'Files', 'sort_weight' => 30],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'files.manage', 'label' => 'Manage files'],
        ];
    }
}
