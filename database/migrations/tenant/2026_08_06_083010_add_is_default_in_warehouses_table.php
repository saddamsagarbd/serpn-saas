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
        Schema::table('warehouses', function (Blueprint $table) {
            if(!Schema::hasColumn('warehouses', 'tenant_id')) $table->string('tenant_id')->nullable()->after('code');
            if(!Schema::hasColumn('warehouses', 'is_default')) $table->boolean('is_default')->default(false)->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            if(Schema::hasColumn('warehouses', 'tenant_id')) $table->dropColumn('tenant_id');
            if(Schema::hasColumn('warehouses', 'is_default')) $table->dropColumn('is_default');
        });
    }
};
