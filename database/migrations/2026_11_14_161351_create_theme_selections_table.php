<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only, same idiom as profile_install_history — the
        // active theme is just whichever row is latest. Switching
        // themes records a new row rather than updating one in place,
        // which gives a free history of what the instance looked like
        // and when, at no extra cost.
        Schema::create('theme_selections', function (Blueprint $table) {
            $table->id();
            $table->string('theme_slug');
            $table->string('set_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_selections');
    }
};
