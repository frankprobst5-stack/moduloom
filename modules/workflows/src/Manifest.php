<?php

namespace Moduloom\Workflows;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'workflows';
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
            ['slug' => 'workflows', 'label' => 'Workflows', 'sort_weight' => 160],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'workflows.manage', 'label' => 'Process workflow requests'],
            ['slug' => 'workflows.view', 'label' => 'Submit and view own requests'],
        ];
    }
}
