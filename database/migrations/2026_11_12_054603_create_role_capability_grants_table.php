<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_capability_grants', function (Blueprint $table) {
            $table->id();
            $table->string('role_slug');
            $table->string('capability_slug');
            $table->string('granted_by_source');
            $table->timestamps();

            $table->foreign('role_slug')
                ->references('role_slug')->on('roles')
                ->cascadeOnDelete();

            $table->foreign('capability_slug')
                ->references('capability_slug')->on('capabilities')
                ->cascadeOnDelete();

            // Invariant 3: a (role, capability) pair can have grants from
            // multiple sources. Revoking one source's grant must only
            // delete that source's row — the capability survives if any
            // other source still grants the same pair. This constraint
            // is what lets that be a simple "does any row remain" check
            // instead of assuming one row's deletion is authoritative.
            $table->unique(
                ['role_slug', 'capability_slug', 'granted_by_source'],
                'role_capability_grants_unique_source'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_capability_grants');
    }
};
