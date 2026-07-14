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
        Schema::create('material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('mpr_no')->unique(); // e.g., MPR-2026-0001
            $table->date('requisition_date');
            $table->foreignId('requested_by')->constrained('users'); // রিকোয়েস্টকারী ইউজার
            $table->string('department')->nullable(); // e.g., Production, Cutting
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'ordered'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requisitions');
    }
};