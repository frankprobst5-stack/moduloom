<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phase 6b: the link invariant 3's instance-scoped roles never
        // had — which logged-in identity holds which role. Many-to-many
        // rather than a single column on users: costs nothing and
        // matches every other join table in this kernel
        // (profile_install_history, theme_selections,
        // role_capability_grants itself).
        Schema::create('user_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_slug');
            $table->timestamps();

            $table->foreign('role_slug')
                ->references('role_slug')->on('roles')
                ->cascadeOnDelete();

            $table->unique(['user_id', 'role_slug']);
        });

        // Backfill for any instance that already registered its owner
        // under Phase 6a, before this table existed. 6a was a single
        // all-or-nothing account gate — whoever registered was, by
        // construction, the one account — so every existing User here
        // becomes Owner. New registrations get this from
        // AuthController::register() directly; this is only for
        // upgrading an instance that got ahead of this migration.
        if (User::query()->exists()) {
            $owner = Role::firstOrCreate(['role_slug' => 'owner'], ['label' => 'Owner']);

            foreach (User::query()->pluck('id') as $userId) {
                $owner->users()->syncWithoutDetaching([$userId]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role_assignments');
    }
};
