<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_code')->unique()->after('id');   // e.g. SUP-9THNT — auto-generated, editable
            $table->string('bank_name')->nullable()->after('lead_time_days');
            $table->string('bank_account_number')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['supplier_code', 'bank_name', 'bank_account_number']);
        });
    }
};
