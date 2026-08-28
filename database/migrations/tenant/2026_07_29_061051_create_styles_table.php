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
        Schema::create('styles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->index(); // Multi-tenant scoping
            $table->foreignId('buyer_id')->constrained('buyers')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->string('style_number', 100);          // e.g., H57-TS-001
            $table->string('product_name');                  // e.g., Ladies Denim Jacket
            $table->decimal('target_price', 12, 4)->default(0.0000);
            $table->string('product_image')->nullable();
            $table->enum('status', ['draft', 'running', 'completed', 'cancelled'])->default('draft');
            $table->text('description')->nullable();                // e.g., Ladies Denim Jacket
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['tenant_id', 'style_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('styles');
    }
};