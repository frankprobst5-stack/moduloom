<?php

namespace Moduloom\Assignments;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'assignments';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function requires(): array
    {
        // Assignments belong to a classroom — a real foreign key, same
        // pattern as Invoicing requiring CRM in Phase 3.
        return ['classrooms'];
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/../database/migrations';
    }

    public function navEntries(): array
    {
        return [
            ['slug' => 'assignments', 'label' => 'Assignments', 'sort_weight' => 130],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'assignments.manage', 'label' => 'Manage assignments'],
            ['slug' => 'assignments.view', 'label' => 'View and submit assignments'],
        ];
    }

    public function description(): string
    {
        return 'Assignments and student submissions.';
    }

    public function category(): string
    {
        return 'Education';
    }
}
