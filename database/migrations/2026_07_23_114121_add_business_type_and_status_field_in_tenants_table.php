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
            if (!Schema::hasColumn('tenants', 'business_type')) {
            // Change string to enum here
                $table->string('business_type', 50)
                    ->nullable()
                    ->after('company_name');
            }
            
            if (!Schema::hasColumn('tenants', 'status')) {
                $table->enum('status', ['active', 'suspended'])
                    ->default('active')
                    ->after('owner_email');
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