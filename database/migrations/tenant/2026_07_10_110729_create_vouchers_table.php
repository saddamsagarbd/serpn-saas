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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique(); // e.g., VOU-2026-0001
            $table->enum('type', ['income', 'expense', 'journal']);
            $table->date('date');
            $table->string('narration')->nullable(); // বিবরণ বা নোট
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};