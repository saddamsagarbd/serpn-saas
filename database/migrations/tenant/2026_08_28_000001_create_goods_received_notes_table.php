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
            $table->string('tenant_id')->index(); // Tenant isolation
            $table->string('grn_no')->unique(); // GRN-2026-0001
            $table->foreignId('purchase_order_id')->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained(); // Direct Supplier Link
            $table->foreignId('warehouse_id')->constrained(); // Selected Warehouse
            $table->date('received_date');
            $table->foreignId('received_by')->constrained('users');
            $table->string('challan_no')->nullable();
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