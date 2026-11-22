<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fed-b: an Owner-managed list of other instances this one
        // chooses to pull content from — never auto-discovered, per
        // FEDERATION_DESIGN.md's "not a SaaS" constraint. Per-peer,
        // per-content-type opt-in (Frank's call) rather than an
        // all-or-nothing subscription.
        Schema::create('federation_peers', function (Blueprint $table) {
            $table->id();
            $table->string('base_url')->unique();
            $table->string('label');
            $table->json('subscribed_content_types');
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('federation_peers');
    }
};
