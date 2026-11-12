<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Invariant 4: this is an append-only audit log of imperative,
        // point-in-time profile applications. It is never consulted to
        // decide current state — installed_modules and module_references
        // are the source of truth for that. This table only answers
        // "what happened and when."
        Schema::create('profile_install_history', function (Blueprint $table) {
            $table->id();
            $table->string('profile_slug');
            $table->string('version');
            $table->string('installed_by')->nullable();
            $table->timestamp('installed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_install_history');
    }
};
