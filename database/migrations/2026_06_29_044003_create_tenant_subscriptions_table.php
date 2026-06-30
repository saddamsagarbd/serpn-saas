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
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            
            // stancl/tenancy প্যাকেজের ডিফল্ট tenant id সাধারণত string বা uuid হয়, তাই string ব্যবহার করা নিরাপদ
            $table->string('tenant_id'); 
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            
            // সাবস্ক্রিপশন লাইফসাইকেল ট্র্যাকিং
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable()->comment('Subscription Expiry Date');
            $table->timestamp('trial_ends_at')->nullable(); // যদি ট্রায়াল পিরিয়ড দিতে চান
            
            $table->enum('status', ['active', 'expired', 'canceled', 'grace_period'])->default('active');
            $table->timestamps();

            // ফরেন কি রিলেশনশিপ (stancl/tenancy এর tenants টেবিলের সাথে)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
