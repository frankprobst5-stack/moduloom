<?php

namespace Moduloom\Projects;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'projects';
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
            ['slug' => 'projects', 'label' => 'Projects', 'sort_weight' => 170],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'projects.manage', 'label' => 'Manage projects'],
        ];
    }

    public function description(): string
    {
        return 'Projects with tasks and assignees.';
    }

    public function category(): string
    {
        return 'Enterprise';
    }
}
