<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phase 6c: who submitted this, so applications.view's "submit
        // and view own applications" grant has something real to scope
        // against. Nullable — Staff can still log an application that
        // came in some other way, with no applicant account behind it.
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
