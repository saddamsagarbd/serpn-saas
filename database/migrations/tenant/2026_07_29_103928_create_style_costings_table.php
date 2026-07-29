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
        Schema::create('style_costings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('style_id')->constrained('styles')->onDelete('cascade');
            $table->string('currency', 10)->default('USD');  // Stores selected currency (USD, BDT, etc.)
            
            // 4 decimal places for precise international fabric/apparel trading pricing
            $table->decimal('target_fob', 12, 4)->default(0.0000); 
            $table->decimal('offered_fob', 12, 4)->default(0.0000); 
            $table->decimal('total_rm_cost', 12, 4)->default(0.0000); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_costings');
    }
};
