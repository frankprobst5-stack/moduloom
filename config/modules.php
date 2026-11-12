<?php

use Moduloom\Notes\Manifest;

// Module registry: slug => its manifest class. Manual for now — one
// entry per module, added by hand. Auto-discovery (scanning installed
// moduloom/* Composer packages for a manifest class) is a reasonable
// later improvement, not required to prove the Phase 1/2/3 loops.
return [
    'notes' => Manifest::class,
    'tasks' => Moduloom\Tasks\Manifest::class,
    'files' => Moduloom\Files\Manifest::class,
    'personal-databases' => Moduloom\PersonalDatabases\Manifest::class,
    'website' => Moduloom\Website\Manifest::class,
    'crm' => Moduloom\Crm\Manifest::class,
    'inventory' => Moduloom\Inventory\Manifest::class,
    'invoicing' => Moduloom\Invoicing\Manifest::class,
];
