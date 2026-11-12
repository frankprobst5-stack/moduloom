<?php

namespace Moduloom\Invoicing;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'invoicing';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function requires(): array
    {
        // Invoices can reference a CRM contact — a real, load-bearing
        // module dependency (a real foreign key), not just bookkeeping.
        // ModuleInstaller installs crm first, so crm_contacts always
        // exists before invoicing's own migration runs.
        return ['crm'];
    }

    public function migrationsPath(): string
    {
        return __DIR__.'/../database/migrations';
    }

    public function navEntries(): array
    {
        return [
            ['slug' => 'invoicing', 'label' => 'Invoicing', 'sort_weight' => 70],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'invoicing.manage', 'label' => 'Manage invoices'],
            ['slug' => 'invoicing.view', 'label' => 'View own invoices'],
        ];
    }
}
