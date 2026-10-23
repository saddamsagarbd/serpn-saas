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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('payment_no'); // e.g. PAY-20261023-0001
            $table->foreignId('supplier_id');
            $table->foreignId('bank_account_id'); // Chart of accounts id (Cash/Bank)
            $table->decimal('total_paid_amount', 15, 4);
            $table->decimal('advance_amount', 15, 4)->default(0);
            $table->decimal('advance_used', 15, 4)->default(0);
            $table->date('payment_date');
            $table->string('payment_method'); // Cheque, Cash, Bank Transfer, etc.
            $table->string('reference_no')->nullable();
            $table->foreignId('voucher_id')->nullable(); // Accounts double entry voucher id
            $table->text('narration')->nullable();
            $table->foreignId('created_by');
            $table->timestamps();
        });

        Schema::create('supplier_payment_items', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('supplier_payment_id')->constrained('supplier_payments')->onDelete('cascade');
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices'); // Kon invoice-er against-e payment hocche
            $table->decimal('allocated_amount', 15, 4)->default(0); // Ei invoice-e koto taka adjust holo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_items');
        Schema::dropIfExists('supplier_payments');
    }
};