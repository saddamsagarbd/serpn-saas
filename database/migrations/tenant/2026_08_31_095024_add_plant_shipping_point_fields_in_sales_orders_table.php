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
        Schema::table('sales_orders', function (Blueprint $table) {            
            if (!Schema::hasColumn('sales_orders', 'style_id')) $table->foreignId('style_id')->nullable()->constrained('styles')->after('tenant_id');
            if (!Schema::hasColumn('sales_orders', 'plant')) $table->string('plant')->nullable()->after('requested_delivery_date');
            if (!Schema::hasColumn('sales_orders', 'shipping_point')) $table->string('shipping_point')->nullable()->after('plant');
            if (!Schema::hasColumn('sales_orders', 'updated_by')) $table->foreignId('updated_by')->nullable()->constrained('users')->nullable()->after('created_by');
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_order_items', 'style_id')) {
                $table->dropForeign(['style_id']);
                $table->dropColumn('style_id');
            }
            if (Schema::hasColumn('sales_order_items', 'plant')) $table->dropColumn('plant');
            if (Schema::hasColumn('sales_order_items', 'shipping_point')) $table->dropColumn('shipping_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'style_id')) {
                $table->dropForeign(['style_id']);
                $table->dropColumn('style_id');
            }
            if (Schema::hasColumn('sales_orders', 'plant')) $table->dropColumn('plant');
            if (Schema::hasColumn('sales_orders', 'shipping_point')) $table->dropColumn('shipping_point');
            if (Schema::hasColumn('sales_orders', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });
    }
};
