<?php

namespace Moduloom\PublicInformation;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'public-information';
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
            ['slug' => 'public-information', 'label' => 'Public information', 'sort_weight' => 190],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'public_information.manage', 'label' => 'Publish public notices'],
        ];
    }

    public function description(): string
    {
        return 'Public notices and announcements.';
    }

    public function category(): string
    {
        return 'Government';
    }
}
