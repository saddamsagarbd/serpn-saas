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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('created_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_orders', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('updated_at')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('created_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_order_items', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('updated_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('purchase_orders', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('purchase_order_items', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });
    }
};
