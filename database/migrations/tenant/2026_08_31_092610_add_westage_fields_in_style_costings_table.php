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
        Schema::table('style_costings', function (Blueprint $table) {
            if (!Schema::hasColumn('style_costings', 'print_wastage')) $table->decimal('print_wastage', 5, 2)->default(0)->after('print_cost');
            if (!Schema::hasColumn('style_costings', 'emb_wastage')) $table->decimal('emb_wastage', 5, 2)->default(0)->after('emb_cost');
            if (!Schema::hasColumn('style_costings', 'wash_wastage')) $table->decimal('wash_wastage', 5, 2)->default(0)->after('wash_cost');
            if (!Schema::hasColumn('style_costings', 'cm_wastage')) $table->decimal('cm_wastage', 5, 2)->default(0)->after('cm_cost');
            if (!Schema::hasColumn('style_costings', 'overhead_wastage')) $table->decimal('overhead_wastage', 5, 2)->default(0)->after('overhead_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('style_costings', function (Blueprint $table) {            
            if (Schema::hasColumn('style_costings', 'print_wastage')) $table->dropColumn('print_wastage');
            if (Schema::hasColumn('style_costings', 'emb_wastage')) $table->dropColumn('emb_wastage');
            if (Schema::hasColumn('style_costings', 'wash_wastage')) $table->dropColumn('wash_wastage');
            if (Schema::hasColumn('style_costings', 'cm_wastage')) $table->dropColumn('cm_wastage');
            if (Schema::hasColumn('style_costings', 'overhead_wastage')) $table->dropColumn('overhead_wastage');
        });
    }
};
