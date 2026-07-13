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
        Schema::create('color_contexts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Olive, Navy Blue, Solid Black
            $table->string('color_code')->nullable(); // hex format e.g., #556B2F
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_contexts');
    }
};
