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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('chart_of_accounts')->restrictOnDelete(); // points at the shared 'Accounts Payable' control account
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->integer('payment_terms_days')->default(0);
            $table->integer('lead_time_days')->nullable();
            $table->string('currency', 3)->default('BDT');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
 
            $table->index('name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
