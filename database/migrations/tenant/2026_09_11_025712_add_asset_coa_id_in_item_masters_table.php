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
        Schema::table('item_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('item_masters', 'asset_coa_id')) {
                $table->foreignId('asset_coa_id')->nullable()->after('tenant_id')->constrained('chart_of_accounts')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_masters', function (Blueprint $table) {
            if (Schema::hasColumn('item_masters', 'asset_coa_id')) {
                $table->dropForeign(['asset_coa_id']);
                $table->dropColumn('asset_coa_id');
            }
        });
    }
};