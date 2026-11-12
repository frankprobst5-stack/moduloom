<?php

use Moduloom\Notes\Manifest;

// Module registry: slug => its manifest class. Manual for now — one
// entry, added by hand per module. Auto-discovery (scanning installed
// moduloom/* Composer packages for a manifest class) is a reasonable
// later improvement, not required to prove the Phase 1 loop.
return [
    'notes' => Manifest::class,
];
