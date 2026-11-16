<?php

use App\Kernel\Reconciler;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill for any instance that already has an 'owner' role
        // from before instance.manage existed as a concept (6a/6b
        // shipped without it). A fresh instance gets this straight from
        // AuthController::register() instead; this only covers
        // upgrading one that got ahead of this migration.
        if (Role::where('role_slug', 'owner')->exists()) {
            app(Reconciler::class)->grantCapability(
                'owner',
                'Owner',
                'instance.manage',
                'Manage the instance',
                'kernel',
            );
        }
    }

    public function down(): void
    {
        app(Reconciler::class)->revokeGrant('owner', 'instance.manage', 'kernel');
    }
};
