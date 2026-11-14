<?php

namespace Moduloom\Classrooms;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'classrooms';
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
            ['slug' => 'classrooms', 'label' => 'Classrooms', 'sort_weight' => 120],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'classrooms.manage', 'label' => 'Manage classrooms'],
        ];
    }

    public function description(): string
    {
        return 'Classrooms with a student roster.';
    }

    public function category(): string
    {
        return 'Education';
    }
}
