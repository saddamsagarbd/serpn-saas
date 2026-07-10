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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Kilogram, Piece, Box
            $table->string('short_name'); // e.g., KG, Pcs, Box
            
            // ERP Standard Fields
            $table->boolean('is_base_unit')->default(true); 
            $table->unsignedBigInteger('base_unit_id')->nullable(); // সাব-ইউনিট হলে তার মেইন বেজ ইউনিট আইডি
            $table->decimal('operator_value', 12, 4)->default(1.0000); // কনভার্সন রেট (e.g., 12.0000)
            $table->enum('operator', ['*', '/'])->default('*'); // গুণ নাকি ভাগ হবে
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // ফরেন কি রিলেশন (সেলফ রেফারেন্সিং)
            $table->foreign('base_unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};