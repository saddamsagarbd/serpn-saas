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
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_no')->unique(); // e.g., GRN-2026-0001
            $table->foreignId('purchase_order_id')->constrained(); // PO reference
            $table->date('received_date');
            $table->foreignId('received_by')->constrained('users');
            $table->string('challan_no')->nullable(); // Supplier delivery challan
            $table->enum('status', ['received', 'partially_received', 'returned'])->default('received');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};