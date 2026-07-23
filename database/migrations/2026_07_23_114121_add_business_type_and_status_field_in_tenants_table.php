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
        Schema::table('tenants', function (Blueprint $table) {
            if(!Schema::hasColumn('tenants', 'business_type')){
                $table->string('business_type', [ 'merchandising', 'real_estate', 'production'])->nullable()->after('company_name');
            }
            if(!Schema::hasColumn('tenants', 'status')){
                $table->enum('status', ['active', 'suspended'])->default('active')->after('owner_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            //
        });
    }
};
