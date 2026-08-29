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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('goods_received_note_id')->nullable()->constrained('goods_received_notes')->nullOnDelete();
            
            $table->string('invoice_no')->unique(); // Vendor's Invoice / Bill Number
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            // Financial Summaries
            $table->decimal('sub_total', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('debit_note_adjusted_amount', 15, 2)->default(0.00); // PR/Debit Note adjustment
            $table->decimal('net_amount', 15, 2)->default(0.00); // Final Payable Amount
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            
            // Status Tracking
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'cancelled'])->default('unpaid');
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
