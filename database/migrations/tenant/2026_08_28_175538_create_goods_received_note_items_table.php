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
        Schema::create('goods_received_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained();
            $table->foreignId('item_id')->constrained('item_masters')->onDelete('cascade');
            
            // Quantity Tracking
            $table->decimal('quantity_received', 15, 2);
            $table->decimal('rejected_qty', 15, 2)->default(0); // Quality check control
            
            // Financial Tracking for Stock Valuation
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // QA Status & Batch
            $table->string('qa_status')->default('Good'); // Good, Damaged, Partial
            $table->string('batch_no')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};