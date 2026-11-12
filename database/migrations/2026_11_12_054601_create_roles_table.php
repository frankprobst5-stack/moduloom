<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles are instance-scoped (invariant 3): whichever module or
        // profile first needs a role creates it here, and later profiles
        // only ever attach capability grants to it — never redefine it.
        Schema::create('roles', function (Blueprint $table) {
            $table->string('role_slug')->primary();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
