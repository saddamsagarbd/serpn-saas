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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 'basic', 'premium', 'enterprise'
            $table->string('title');
            
            // মূল্য ট্র্যাক করার জন্য 'string' এর বদলে decimal বা integer ব্যবহার করা বেস্ট প্র্যাকটিস
            $table->decimal('price', 8, 2)->default(0.00); 
            $table->string('currency')->default('BDT'); // ভবিষ্যতে ইন্টারন্যাশনাল পেমেন্টের জন্য সুবিধা হবে
            
            // সাবস্ক্রিপশন মেয়াদ (Billing Cycle)
            $table->integer('billing_interval')->default(1); // 1, 3, 12
            $table->enum('billing_period', ['day', 'week', 'month', 'year'])->default('month'); // মাসিক বা বার্ষিক বিলিং
            
            // লিমিটেশন ট্র্যাকিং (SaaS অ্যাপের জন্য সবচেয়ে গুরুত্বপূর্ণ)
            $table->integer('max_tenants_users')->default(-1)->comment('-1 means unlimited'); // ওই অর্গানাইজেশন সর্বোচ্চ কয়টা ইউজার অ্যাকাউন্ট খুলতে পারবে
            $table->integer('max_invoice_limit')->default(-1); // মাসে সর্বোচ্চ কয়টা ইনভয়েস জেনারেট করতে পারবে
            $table->integer('max_product_limit')->default(-1); // সর্বোচ্চ কয়টি প্রোডাক্ট ইনভেন্টরিতে রাখতে পারবে
            
            $table->boolean('best_deal')->default(false);

            // এক্সট্রা ফিচার (JSON ফিল্ডে ফিচারগুলোর অন/অফ লিস্ট রাখতে পারেন, যেমন: ['barcode_scanner' => true, 'website_integration' => false])
            $table->json('features')->nullable(); 

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
