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
        Schema::table('production_bom_items', function (Blueprint $table) {
            if(!Schema::hasColumn('production_bom_items', 'color_id')){
                $table->foreignId('color_id')->nullable()->after("color_name")->constrained('color_contexts')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_bom_items', function (Blueprint $table) {
            if(Schema::hasColumn('production_bom_items', 'color_id')){
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }
        });
    }
};