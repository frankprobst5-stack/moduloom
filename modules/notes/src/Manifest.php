<?php

namespace Moduloom\Notes;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'notes';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function requires(): array
    {
        // Chosen as Moduloom's first real module specifically because
        // it has none — see MASTER_ROADMAP.md Phase 1.
        return [];
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/../database/migrations';
    }

    public function navEntries(): array
    {
        return [
            ['slug' => 'notes', 'label' => 'Notes', 'sort_weight' => 10],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'notes.manage', 'label' => 'Manage notes'],
        ];
    }

    public function description(): string
    {
        return 'Quick personal notes.';
    }

    public function category(): string
    {
        return 'Productivity';
    }
}
