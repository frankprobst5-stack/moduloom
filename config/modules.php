<?php

use Moduloom\Notes\Manifest;

// Module registry: slug => its manifest class. Manual for now — one
// entry per module, added by hand. Auto-discovery (scanning installed
// moduloom/* Composer packages for a manifest class) is a reasonable
// later improvement, not required to prove the Phase 1-4 loops.
return [
    'notes' => Manifest::class,
    'tasks' => Moduloom\Tasks\Manifest::class,
    'files' => Moduloom\Files\Manifest::class,
    'personal-databases' => Moduloom\PersonalDatabases\Manifest::class,
    'website' => Moduloom\Website\Manifest::class,
    'crm' => Moduloom\Crm\Manifest::class,
    'inventory' => Moduloom\Inventory\Manifest::class,
    'invoicing' => Moduloom\Invoicing\Manifest::class,
    'forums' => Moduloom\Forums\Manifest::class,
    'membership' => Moduloom\Membership\Manifest::class,
    'events' => Moduloom\Events\Manifest::class,
    'directories' => Moduloom\Directories\Manifest::class,
    'classrooms' => Moduloom\Classrooms\Manifest::class,
    'assignments' => Moduloom\Assignments\Manifest::class,
    'knowledge-base' => Moduloom\KnowledgeBase\Manifest::class,
    'documents' => Moduloom\Documents\Manifest::class,
    'workflows' => Moduloom\Workflows\Manifest::class,
    'projects' => Moduloom\Projects\Manifest::class,
    'applications' => Moduloom\Applications\Manifest::class,
    'public-information' => Moduloom\PublicInformation\Manifest::class,
    'forms' => Moduloom\Forms\Manifest::class,
    'records' => Moduloom\Records\Manifest::class,
];
