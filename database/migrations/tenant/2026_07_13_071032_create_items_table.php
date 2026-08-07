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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            
            // SKU Pattern: Style + Color + Size
            $table->string('sku')->unique(); // e.g. H57-TS-001-BLK-L
            $table->string('name'); // Item Description
            
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            
            // Foreign Keys linked with Style, Color, Size
            $table->foreignId('style_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('color_contexts')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('size_charts')->nullOnDelete();
            $table->foreignId('fabric_spec_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('purchase_price', 15, 2)->default(0.00);
            $table->decimal('sale_price', 15, 2)->default(0.00);
            $table->decimal('reorder_level', 15, 2)->default(10.00);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};