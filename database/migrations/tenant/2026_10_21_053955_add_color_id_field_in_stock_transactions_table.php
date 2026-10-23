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
        Schema::table('stock_transactions', function (Blueprint $table) {
            if(!Schema::hasColumn('stock_transactions', 'color_id')){
                $table->foreignId('color_id')->nullable()->after("item_id")->constrained('color_contexts')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            if(Schema::hasColumn('stock_transactions', 'color_id')){
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }
        });
    }
};