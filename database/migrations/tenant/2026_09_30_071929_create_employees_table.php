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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('tenant_id');
            
            // Employee Unique Identification
            $table->string('employee_id')->unique(); // e.g. EMP-1001
            $table->string('sensor_id')->nullable();
            $table->string('name');
            
            // Core HRM Foreign Keys
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            
            // Designation is Optional (Nullable)
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();

            // Contact Information
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('emergency_contact')->nullable();
            
            // Additional Optional Details
            $table->date('joining_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment("0=In-active; 1=active; 2=suspend; 3=retired;");

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes(); // Optional: Employee soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
