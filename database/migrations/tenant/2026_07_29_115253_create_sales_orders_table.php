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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('sales_org')->default('House 57 Bangladesh'); //
            $table->string('distribution_channel'); // Export / Domestic
            $table->string('job_mode');             // FOB / CMPTW / CM
            $table->string('division');             // Merchant Team
            $table->foreignId('buyer_id')->constrained('buyers'); // Sold-to Party
            $table->string('ship_to_party');        //
            $table->string('buyer_po_number')->index(); //
            $table->date('po_received_date');       //
            $table->date('advance_receive_date')->nullable(); //
            $table->date('requested_delivery_date'); //
            $table->string('currency')->default('USD'); //
            $table->string('status')->default('Draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
