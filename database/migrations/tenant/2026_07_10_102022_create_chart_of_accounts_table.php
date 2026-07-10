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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Sales Revenue, Office Rent, Petty Cash
            $table->string('code')->unique(); // Unique Account Code e.g., 4001, 5002 (ERP Standard)
            
            // Core Accounting Types
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            
            // Sub-account Structure (Tree View)
            $table->unsignedBigInteger('parent_id')->nullable(); 
            
            // Balance Control
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_system_defined')->default(false); // সিস্টেমের ডিফল্ট হেডগুলো যাতে ইউজার ডিলিট না করতে পারে
            $table->timestamps();

            // Foreign Key Configuration
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};