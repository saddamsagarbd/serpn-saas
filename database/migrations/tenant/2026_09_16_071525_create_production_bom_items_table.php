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
        Schema::create('production_bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_bom_id')->constrained('production_boms')->onDelete('cascade');
            
            // Link to Pre-costing BOM Item (Style -> StyleCosting -> BomItem)
            $table->foreignId('bom_item_id')->constrained('bom_items')->onDelete('cascade');

            // Link to MPR Order Line (Specific Color/Size Matrix)
            $table->foreignId('sales_order_item_id')->nullable()->constrained('sales_order_items')->onDelete('cascade');
            
            // Procurement Execution Values
            $table->decimal('consumption', 10, 4)->default(0.0000);
            $table->decimal('unit_price', 10, 4)->default(0.0000);
            $table->decimal('total_qty', 12, 2)->default(0.00);
            $table->decimal('total_budget', 12, 2)->default(0.00);
            
            // Procurement & Accounting Details
            $table->string('po_number')->nullable();
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_bom_items');
    }
};
