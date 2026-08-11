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
        Schema::create('style_costings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('style_id')->constrained('styles')->onDelete('cascade');
            $table->string('currency', 10)->default('USD'); 

            // 1. Core Cost Breakdown
            $table->decimal('total_rm_cost', 12, 4)->default(0.0000);      // Total Raw Material (Fabric + Trims + Packing)
            $table->decimal('total_service_cost', 12, 4)->default(0.0000); // Total Services (CM + Print + Wash + Overhead)
            $table->decimal('base_cost', 12, 4)->default(0.0000);          // Total Base Cost (RM Cost + Service Cost)

            // 2. Markups & Taxes (Percentages)
            $table->decimal('revenue_percent', 5, 2)->default(0.00);       // Revenue / Profit %
            $table->decimal('ait_percent', 5, 2)->default(0.00);           // Advance Income Tax %
            $table->decimal('vat_percent', 5, 2)->default(0.00);           // Value Added Tax %
            $table->decimal('commercial_percent', 5, 2)->default(0.00);    // Freight & Commercial Charges % (Optional)

            // 3. Markups & Taxes (Calculated Amounts)
            $table->decimal('revenue_amount', 12, 4)->default(0.0000);
            $table->decimal('ait_amount', 12, 4)->default(0.0000);
            $table->decimal('vat_amount', 12, 4)->default(0.0000);

            // 4. Final Pricing / FOB Summary
            $table->decimal('calculated_fob', 12, 4)->default(0.0000);    // Auto-calculated FOB price
            $table->decimal('target_fob', 12, 4)->default(0.0000);        // Buyer Given Target Price
            $table->decimal('offered_fob', 12, 4)->default(0.0000);       // Final Quoted/Offered Price (Rounded)

            // 5. Workflow & Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Unique Constraint: প্রতিটি Style-এর জন্য ১টিই Costing Record থাকবে
            $table->unique(['tenant_id', 'style_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_costings');
    }
};
