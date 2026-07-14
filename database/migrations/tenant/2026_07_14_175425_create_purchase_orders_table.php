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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no')->unique(); // e.g., PO-2664 
            $table->foreignId('supplier_id')->constrained(); //
            $table->foreignId('material_requisition_id')->nullable()->constrained(); // MPR-এর রেফারেন্স
            $table->date('po_date');
            $table->date('delivery_date')->nullable(); // 
            
            // বিলিং সামারি (ক্লায়েন্ট ফাইলের সাথে মিল রেখে) 
            $table->decimal('subtotal', 15, 2)->default(0.00); // শুধু প্রোডাক্টের মোট দাম
            $table->decimal('transport_cost', 15, 2)->default(0.00);  // ট্রান্সপোর্ট বিল (যেমন: ১,০০০ টাকা)
            $table->decimal('loader_bill', 15, 2)->default(0.00);     // লেবার/কুলি বিল (যেমন: ৪০০ টাকা)
            $table->decimal('inspection_bill', 15, 2)->default(0.00); // ফেব্রিক ইন্সপেকশন বিল (যেমন: ১৮০ টাকা)
            $table->decimal('extra_charges', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00); // 
            $table->decimal('grand_total', 15, 2)->default(0.00); // সর্বমোট বিল (TTL INVOICE VALUE) 
            $table->decimal('total_paid', 15, 2)->default(0.00); // পেমেন্ট ট্র্যাকিংয়ের জন্য 
            $table->decimal('due_amount', 15, 2)->default(0.00); // বাকি টাকা 

            // শর্তাবলী (Terms & Conditions) [cite: 50]
            $table->string('payment_terms_text')->nullable(); // e.g., "60% Advance, 40% After Delivery" [cite: 57, 58]
            $table->enum('status', ['draft', 'pending', 'approved', 'partially_received', 'received', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};