<?php

namespace Moduloom\Events;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'events';
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
            ['slug' => 'events', 'label' => 'Events', 'sort_weight' => 100],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'events.manage', 'label' => 'Manage events'],
        ];
    }

    public function description(): string
    {
        return 'Events with registration.';
    }

    public function category(): string
    {
        return 'Community';
    }
}
