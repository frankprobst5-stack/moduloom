<?php

namespace Moduloom\Applications;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'applications';
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
            ['slug' => 'applications', 'label' => 'Applications', 'sort_weight' => 180],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'applications.manage', 'label' => 'Review applications'],
            ['slug' => 'applications.view', 'label' => 'Submit and view own applications'],
        ];
    }

    public function description(): string
    {
        return 'Review submitted applications.';
    }

    public function category(): string
    {
        return 'Enterprise';
    }
}
