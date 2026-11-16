<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phase 6c: which student this submission belongs to, so
        // assignments.view's "view and submit own assignments" grant
        // has something real to scope against. Assignments themselves
        // stay unowned — a student's "own" thing is their submission,
        // not the assignment every student in the classroom shares.
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('assignment_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
