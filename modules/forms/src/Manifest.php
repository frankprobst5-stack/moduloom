<?php

namespace Moduloom\Forms;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'forms';
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
            ['slug' => 'forms', 'label' => 'Forms', 'sort_weight' => 200],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'forms.manage', 'label' => 'Manage forms and submissions'],
        ];
    }
}
