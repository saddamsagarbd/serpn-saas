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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('style_costing_id')->constrained('style_costings')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item_masters')->onDelete('cascade');
            $table->string('item_description');
            
            // Foreign keys pointing to your master variant tables
            $table->foreignId('color_id')->nullable()->constrained('color_contexts')->NullonDelete();
            $table->foreignId('size_id')->nullable()->constrained('size_charts')->NullonDelete();
            
            $table->decimal('consumption', 12, 4)->default(0.0000); // User's 'qty' input field
            $table->decimal('unit_price', 12, 4)->default(0.0000);  // User's 'cost' input field
            $table->decimal('total_cost', 12, 4)->default(0.0000);  // Pre-calculated line cost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};