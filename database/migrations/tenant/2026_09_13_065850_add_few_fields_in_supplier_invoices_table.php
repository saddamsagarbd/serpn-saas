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
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_invoices', 'purchase_order_id')) $table->foreignId('purchase_order_id')->nullable()->after('tenant_id')->constrained('purchase_orders')->nullOnDelete();

            if (!Schema::hasColumn('supplier_invoices', 'other_charges')) $table->decimal('other_charges', 15, 2)->default(0.00)->after('discount_amount');

            $table->unique(['tenant_id', 'invoice_no', 'supplier_id'], 'supplier_invoices_tenant_invoice_supplier_unique');
        });

        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_invoice_items', 'grn_item_id')) $table->foreignId('grn_item_id')->nullable()->after('supplier_invoice_id')->constrained('goods_received_note_items')->nullOnDelete();
            if (!Schema::hasColumn('supplier_invoice_items', 'style_id')) $table->foreignId('style_id')->nullable()->after('item_id')->constrained('styles')->nullOnDelete();
            if (!Schema::hasColumn('supplier_invoice_items', 'color_id')) $table->foreignId('color_id')->nullable()->after('style_id')->constrained('color_contexts')->nullOnDelete();
            if (!Schema::hasColumn('supplier_invoice_items', 'size_id')) $table->foreignId('size_id')->nullable()->after('color_id')->constrained('size_charts')->nullOnDelete();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropUnique('supplier_invoices_tenant_invoice_supplier_unique');

            if (Schema::hasColumn('supplier_invoices', 'purchase_order_id')) {
                $table->dropForeign(['purchase_order_id']);
                $table->dropColumn('purchase_order_id');
            }

            if (Schema::hasColumn('supplier_invoices', 'other_charges')) {
                $table->dropColumn('other_charges');
            }
        });

        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_invoice_items', 'grn_item_id')) {
                $table->dropForeign(['grn_item_id']);
                $table->dropColumn('grn_item_id');
            }

            if (Schema::hasColumn('supplier_invoice_items', 'style_id')) {
                $table->dropForeign(['style_id']);
                $table->dropColumn('style_id');
            }

            if (Schema::hasColumn('supplier_invoice_items', 'color_id')) {
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }

            if (Schema::hasColumn('supplier_invoice_items', 'size_id')) {
                $table->dropForeign(['size_id']);
                $table->dropColumn('size_id');
            }
        });
    }
};
