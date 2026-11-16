<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phase 6c: which Customer this invoice belongs to, so
        // invoicing.view's "view own invoices" grant has something real
        // to scope against. Nullable — Staff can issue an invoice to a
        // customer_name before that customer necessarily has an account.
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('crm_contact_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
