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
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('item_master_id')->constrained('item_masters')->onDelete('cascade');
            
            // Pattern: Style-Color-Size (e.g., H57-TS-001-BLK-L)
            $table->string('sku')->unique();
            
            // Fixed Foreign Key constraints instead of plain string
            $table->foreignId('color_id')->nullable()->constrained('color_contexts')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('size_charts')->nullOnDelete();
            
            // Price Breakdown for PO and SO
            $table->decimal('purchase_price', 15, 4)->default(0.0000);
            $table->decimal('sale_price', 15, 4)->default(0.0000);
            $table->decimal('reorder_level', 15, 2)->default(10.00);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};