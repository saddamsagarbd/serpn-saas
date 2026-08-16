<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('chart_of_accounts', 'tenant_id')) {                
                $table->string('tenant_id')->nullable()->index()->after('id');
            }
        });

        if (function_exists('tenant') && tenant('id')) {
            DB::table('chart_of_accounts')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => tenant('id')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'tenant_id')) { 
                $table->dropColumn('tenant_id');
            }
        });
    }
};