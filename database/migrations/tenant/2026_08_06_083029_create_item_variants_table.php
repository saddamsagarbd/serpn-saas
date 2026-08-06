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
            $table->string('tenant_id');
            $table->foreignId('item_master_id')->constrained('item_masters')->onDelete('cascade');
            $table->string('sku')->unique(); // e.g., RM-FAB-001-BLK-MEDIUM
            $table->string('color')->nullable(); // Black, White, Multi Color
            $table->string('size')->nullable(); // Medium, Large, ALL
            $table->decimal('unit_price', 15, 4)->default(0);
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
