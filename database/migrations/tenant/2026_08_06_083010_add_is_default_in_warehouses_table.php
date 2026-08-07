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
            if(!Schema::hasColumn('warehouses', 'created_by')) $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            if(!Schema::hasColumn('warehouses', 'updated_by')) $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
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
            if(Schema::hasColumn('warehouses', 'created_by')) {                 
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if(Schema::hasColumn('warehouses', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });
    }
};