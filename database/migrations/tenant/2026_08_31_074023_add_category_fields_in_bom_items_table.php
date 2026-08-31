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
            if (!Schema::hasColumn('bom_items', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('style_costing_id')->constrained('categories')->onDelete('cascade');
            }
            if (!Schema::hasColumn('bom_items', 'category_name')) {
                $table->string('category_name')->nullable()->after('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_items', function (Blueprint $table) {
            if (Schema::hasColumn('bom_items', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('bom_items', 'category_name')) {
                $table->dropColumn('category_name');
            }
        });
    }
};
