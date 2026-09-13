<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_invoices', 'voucher_id')) {
                $table->foreignId('voucher_id')->nullable()->after('tenant_id')->constrained('vouchers')->nullOnDelete();
            }
            if (!Schema::hasColumn('supplier_invoices', 'tax_rate')) {
                $table->decimal('tax_rate', 15, 2)->default(0.00)->after('sub_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('supplier_invoices', 'voucher_id')) {
            try {
                Schema::table('supplier_invoices', function (Blueprint $table) {
                    $table->dropForeign(['voucher_id']);
                });
            } catch (\Exception $e) {
                // Foreign key না থাকলে বা ড্রপ হয়ে থাকলে ইগনোর করবে
                Log::info('Failed to drop foreignId: '. $e->getMessage());
            }
        }
        Schema::table('supplier_invoices', function (Blueprint $table) {

            if (Schema::hasColumn('supplier_invoices', 'voucher_id')) {
                $table->dropColumn('voucher_id');
            }

            if (Schema::hasColumn('supplier_invoices', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });
    }
};
