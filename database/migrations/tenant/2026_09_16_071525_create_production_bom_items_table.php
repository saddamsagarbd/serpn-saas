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
            
            // Original Pre-costing Style BOM Item-er Reference (Nullable cause Processing Service rows won't have it)
            $table->foreignId('item_id')->nullable()->constrained('item_masters')->onDelete('set null');

            // Classification
            $table->enum('cost_type', ['Material', 'Processing'])->default('Material');
            $table->string('cost_head')->nullable(); // Fabric, Print, Wash, CM etc.
            
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('category_name')->nullable();
            
            // Explicit Name String (Snapshot storage for fast load & PDF export)
            $table->string('item_name')->nullable(); 

            // MPR / Matrix Breakdown
            $table->string('matrix_target')->default('ALL');
            $table->foreignId('sales_order_item_id')->nullable()->constrained('sales_order_items')->onDelete('set null');
            $table->string('color_name')->nullable();
            $table->integer('garment_qty')->default(0);

            // Amounts & Pricing
            $table->decimal('consumption', 12, 4)->default(0.0000);
            $table->decimal('req_qty', 12, 2)->default(0.00);
            $table->decimal('unit_price', 12, 4)->default(0.0000);
            $table->decimal('total_cost', 15, 2)->default(0.00);

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