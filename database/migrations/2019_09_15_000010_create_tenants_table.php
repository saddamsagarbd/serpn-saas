<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g., 'acme', 'bata' (এটিই সাবডোমেইন এবং ডাটাবেজ প্রিফিক্স হিসেবে কাজ করবে)
    
            // প্ল্যানের সাথে টেন্যান্ট ট্যাগ করার জন্য
            $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null');
            
            // ভেন্ডর কোম্পানির বেসিক ইনফরমেশন
            $table->string('company_name');
            $table->string('owner_name');
            $table->string('owner_phone')->unique();
            $table->string('owner_email')->unique();
            
            $table->json('data')->nullable(); // stancl/tenancy এর ইন্টারনাল ডাটা স্টোরেজ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
