<?php

namespace Moduloom\KnowledgeBase;

use App\Kernel\ModuleManifest;

class Manifest implements ModuleManifest
{
    public function slug(): string
    {
        return 'knowledge-base';
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
            ['slug' => 'knowledge-base', 'label' => 'Knowledge base', 'sort_weight' => 140],
        ];
    }

    public function capabilities(): array
    {
        return [
            ['slug' => 'knowledge_base.manage', 'label' => 'Manage knowledge base articles'],
        ];
    }

    public function description(): string
    {
        return 'Published help articles.';
    }

    public function category(): string
    {
        return 'Education';
    }
}
