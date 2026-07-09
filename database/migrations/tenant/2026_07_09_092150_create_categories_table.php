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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // টেন্যান্টের নিজস্ব ডেটাবেস হওয়ায় এখানে কোনো tenant_id কলামের প্রয়োজন নেই
            $table->integer('serial_number')->unique()->nullable(); // CAT-101 মেইনটেইনের জন্য
            $table->string('code')->unique()->nullable(); 
            $table->string('name');
            $table->string('slug')->unique(); // প্রতি টেন্যান্ট ডেটাবেসে স্লাগ ইউনিক হবে
            
            // Self-referential hierarchy (প্যারেন্ট ক্যাটাগরি রিলেশন)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete(); 

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
