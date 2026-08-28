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
        Schema::table('goods_received_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_received_notes', 'voucher_id')) {
                $table->foreignId('voucher_id')
                  ->nullable()
                  ->after('challan_no')
                  ->constrained('vouchers')
                  ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            if (Schema::hasColumn('goods_received_notes', 'voucher_id')) {
                // foreign key constraint drop
                $table->dropForeign(['voucher_id']); 
                
                // column drop
                $table->dropColumn('voucher_id');
            }
        });
    }
};