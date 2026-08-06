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
        Schema::create('item_masters', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('code')->nullable(); // e.g., RM-FAB-001
            $table->string('name'); // e.g., 100% Cotton Single Jersey Fabric
            $table->string('item_type'); // Fabric, Trims/Accessories, Finished Goods
            $table->string('unit')->default('Pcs'); // Pcs, Yds, Kg, Cones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_masters');
    }
};
