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
            $table->string('tenant_id')->index();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('item_masters')->nullOnDelete();
            $table->foreignId('color_id')->nullable();
            $table->foreignId('size_id')->nullable();
            $table->foreignId('unit_id')->nullable();
            $table->decimal('mpr_qty', 15, 4)->default(0.00);
            $table->decimal('order_qty', 15, 4)->default(0.00);
            $table->decimal('unit_price', 15, 4)->default(0.00);
            $table->decimal('total_price', 15, 2)->default(0.00);
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