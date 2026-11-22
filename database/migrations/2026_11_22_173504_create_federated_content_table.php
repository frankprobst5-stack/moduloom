<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fed-b: the local cache of a peer's own public content,
        // fetched on our own schedule (pull, not push). Unique on
        // (peer, canonical_url) is what makes re-fetching the same
        // peer idempotent — the same convention Reconciler's own
        // firstOrCreate/unique-constraint pattern already uses
        // throughout the kernel.
        Schema::create('federated_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('federation_peer_id')->constrained()->cascadeOnDelete();
            $table->string('content_type');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->string('canonical_url');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['federation_peer_id', 'canonical_url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('federated_content');
    }
};
