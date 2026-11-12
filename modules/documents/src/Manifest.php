<?php

namespace Moduloom\Documents;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'documents';
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
            ['slug' => 'documents', 'label' => 'Documents', 'sort_weight' => 150],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'documents.manage', 'label' => 'Manage documents'],
        ];
    }
}
