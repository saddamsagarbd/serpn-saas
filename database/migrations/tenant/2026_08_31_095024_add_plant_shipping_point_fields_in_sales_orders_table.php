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
            if (!Schema::hasColumn('sales_orders', 'plant')) $table->string('plant')->nullable()->after('requested_delivery_date');
            if (!Schema::hasColumn('sales_orders', 'shipping_point')) $table->string('shipping_point')->nullable()->after('plant');
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
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
            if (Schema::hasColumn('sales_orders', 'plant')) $table->dropColumn('plant');
            if (Schema::hasColumn('sales_orders', 'shipping_point')) $table->dropColumn('shipping_point');
        });
    }
};
