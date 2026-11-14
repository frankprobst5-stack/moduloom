<?php

namespace Moduloom\Tasks;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'tasks';
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
            ['slug' => 'tasks', 'label' => 'Tasks', 'sort_weight' => 20],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'tasks.manage', 'label' => 'Manage tasks'],
        ];
    }

    public function description(): string
    {
        return 'Simple to-do lists.';
    }

    public function category(): string
    {
        return 'Productivity';
    }
}
