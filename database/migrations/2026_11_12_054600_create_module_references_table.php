<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_references', function (Blueprint $table) {
            $table->id();
            $table->string('module_slug');
            $table->enum('reference_type', ['profile', 'manual', 'module_dependency']);
            $table->string('reference_identifier');
            $table->timestamps();

            $table->foreign('module_slug')
                ->references('module_slug')->on('installed_modules')
                ->cascadeOnDelete();

            // Invariant 1: a module is only eligible for removal when it has
            // zero DISTINCT referrers. This constraint is what makes that
            // true — without it, re-applying the same profile twice (which
            // invariant 4 requires to be safe) could insert duplicate rows
            // and "reference count" would stop meaning anything.
            $table->unique(
                ['module_slug', 'reference_type', 'reference_identifier'],
                'module_references_unique_referrer'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_references');
    }
};
