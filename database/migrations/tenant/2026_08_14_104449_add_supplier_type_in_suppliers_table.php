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
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'tenant_id')) {                
                $table->string('tenant_id')->nullable()->index()->after('id');
            }
            
            if(!Schema::hasColumn('suppliers', 'supplier_type')){                
                $table->string('supplier_type')->default('general')->after('tax_id');
            }
            
            if(!Schema::hasColumn('suppliers', 'created_by')){                
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            }
            
            if(!Schema::hasColumn('suppliers', 'updated_by')){            
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
            }
        });

        if (function_exists('tenant') && tenant('id')) {
            DB::table('suppliers')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => tenant('id')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'tenant_id')) { 
                $table->dropColumn('tenant_id');
            }
            
            if(Schema::hasColumn('suppliers', 'supplier_type')){                
                $table->dropColumn('supplier_type');
            }
            if(Schema::hasColumn('suppliers', 'created_by')){
                $table->dropConstrainedForeignId('created_by');
            }
            if(Schema::hasColumn('suppliers', 'updated_by')){
                $table->dropConstrainedForeignId('updated_by');
            }
        });
    }
};