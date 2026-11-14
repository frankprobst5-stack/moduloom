<?php

namespace Moduloom\Membership;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'membership';
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
            ['slug' => 'membership', 'label' => 'Membership', 'sort_weight' => 90],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'membership.manage', 'label' => 'Manage memberships'],
        ];
    }

    public function description(): string
    {
        return 'Member records with tiers and status.';
    }

    public function category(): string
    {
        return 'Community';
    }
}
