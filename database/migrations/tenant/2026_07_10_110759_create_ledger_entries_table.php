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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('chart_of_account_id'); // COA Head ID
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};