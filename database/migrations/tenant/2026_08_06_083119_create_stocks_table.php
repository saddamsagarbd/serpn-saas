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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('item_variant_id')->constrained('item_variants')->onDelete('cascade');
            
            $table->decimal('available_qty', 15, 4)->default(0.0000);
            $table->decimal('reserved_qty', 15, 4)->default(0.0000);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // একই Warehouse এ একই Variant এর একাধিক Duplicate Row এড়াতে
            $table->unique(['tenant_id', 'warehouse_id', 'item_variant_id'], 'wh_variant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};