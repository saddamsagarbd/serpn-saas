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
            $table->string('item_color')->nullable()->after('item_name');
            $table->decimal('wastage_percent', 8, 2)->default(0)->after('consumption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_bom_items', function (Blueprint $table) {
            $table->dropColumn(['item_color', 'wastage_percent']);
        });
    }
};