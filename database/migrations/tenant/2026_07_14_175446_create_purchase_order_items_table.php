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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained(); // items টেবিল থেকে
            
            // কাপড়ের বিশেষ প্রোপার্টি (পিডিএফ বুকিং ফাইলের সাথে মিল রেখে) 
            $table->string('wash_type')->nullable(); // e.g., Silicon wash
            $table->string('style_id')->nullable(); // e.g., NOVO
            $table->string('color_id')->nullable(); // e.g., Olive, Blue
            $table->string('fabric_code')->nullable(); // e.g., 20x16 Twill
            
            $table->decimal('quantity', 15, 2); // অর্ডারকৃত পরিমাণ 
            $table->decimal('unit_price', 15, 2); // একক প্রতি মূল্য 
            $table->decimal('discount', 15, 2)->default(0.00); // আইটেমভিত্তিক ডিসকাউন্ট 
            $table->decimal('total_cost', 15, 2); // আইটেমের মোট মূল্য (TTL Cost) 
            $table->decimal('quantity_received', 15, 2)->default(0.00); // GRN ট্র্যাকিং করার জন্য
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};