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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy Support
            $table->string('tenant_id')->index();

            // Direct Relational Foreign Keys
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item_masters')->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('restrict');

            // Polymorphic Relation (GRN, Issue, Transfer, Invoice ইত্যাদির সাথে ডায়নামিক লিঙ্ক করার জন্য)
            // reference_type: App\Models\GoodsReceivedNote, reference_id: 10
            $table->nullableMorphs('reference');

            // Transaction Details
            // Transaction Types: 'IN' (GRN/Purchase), 'OUT' (Issue/Sale), 'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT'
            $table->string('type', 20)->index(); 
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('total_price', 15, 4)->default(0);

            // Additional Info
            $table->string('batch_no')->nullable();
            $table->date('transaction_date')->index();
            $table->text('remarks')->nullable();

            // User Audit Trails
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();

            // Indexes for fast querying & reporting
            $table->index(['tenant_id', 'item_id', 'warehouse_id']);
            $table->index(['tenant_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};