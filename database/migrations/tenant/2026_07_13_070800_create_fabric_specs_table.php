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
        Schema::create('fabric_specs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 20x16 Twill (98% Cotton 2% Spandex), Poly Kotet
            $table->string('gsm')->nullable(); // e.g., 275 gsm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_specs');
    }
};
