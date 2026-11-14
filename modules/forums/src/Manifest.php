<?php

namespace Moduloom\Forums;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'forums';
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
            ['slug' => 'forums', 'label' => 'Forums', 'sort_weight' => 80],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'forums.manage', 'label' => 'Moderate forums'],
            ['slug' => 'forums.view', 'label' => 'View and post in forums'],
        ];
    }

    public function description(): string
    {
        return 'Threaded discussion boards.';
    }

    public function category(): string
    {
        return 'Community';
    }
}
