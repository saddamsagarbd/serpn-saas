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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('po_no')->unique(); // e.g., PO-2026-001
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete(); // style_id রেফারেন্স
            $table->date('po_date');
            $table->date('delivery_date')->nullable();
            
            // বিলিং সামারি
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('transport_cost', 15, 2)->default(0.00);
            $table->decimal('loader_bill', 15, 2)->default(0.00);
            $table->decimal('inspection_bill', 15, 2)->default(0.00);
            $table->decimal('extra_charges', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('total_paid', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);

            // শর্তাবলী ও স্ট্যাটাস
            $table->string('payment_terms_text')->nullable(); // e.g., 60% Advance
            $table->enum('status', ['draft', 'pending', 'approved', 'partially_received', 'received', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable(); // notes এর জায়গায় remarks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};