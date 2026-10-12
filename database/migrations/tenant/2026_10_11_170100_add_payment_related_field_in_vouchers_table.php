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
        Schema::table('vouchers', function (Blueprint $table) {
            // অ্যাকাউন্ট হেড / ক্যাটাগরি (ইনকাম বা এক্সপেন্স সেক্টর)
            if(!Schema::hasColumn('vouchers', 'chart_of_account_id')) {
                $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete()->after('date');
            }
            
            // ক্যাশ, ব্যাংক বা MFS লেজার অ্যাকাউন্ট (যেখান থেকে টাকা এসেছে বা গেছে)
            if(!Schema::hasColumn('vouchers', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete()->after('chart_of_account_id');
            }
            
            // পেমেন্ট টাইপ (Advance, Partial, Final)
            if(!Schema::hasColumn('vouchers', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_method_id'); 
            }
            
            // পেমেন্ট গেটওয়ে (Cash, NPSB, RTGS, Cheque, bKash, Nagad ইত্যাদি)
            if(!Schema::hasColumn('vouchers', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_type'); 
            }
            
            // চেক নম্বর বা ট্রানজেকশন আইডি / রেফারেন্স
            if(!Schema::hasColumn('vouchers', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_gateway'); 
            }
            
            // ক্লায়েন্ট বা ভেন্ডরের নাম (Buyer / Vendor Name)
            if(!Schema::hasColumn('vouchers', 'party_name')) {            
                $table->string('party_name')->nullable()->after('reference_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['chart_of_account_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['chart_of_account_id', 'payment_method_id', 'payment_type', 'payment_gateway', 'reference_no', 'party_name']);
        });
    }
};