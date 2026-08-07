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
        Schema::table('bom_items', function (Blueprint $table) {
            if(!Schema::hasColumn('bom_items', 'item_id')) {
                $table->foreignId('item_id')->nullable()->after('style_costing_id')->constrained('item_masters')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_items', function (Blueprint $table) {
            if(Schema::hasColumn('bom_items', 'item_id')) {                
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
        });
    }
};