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
            if(!Schema::hasColumn('style_costings', 'print_cost')){                
                $table->decimal('print_cost', 12, 4)->default(0.0000)->after('total_rm_cost');
            }
            if(!Schema::hasColumn('style_costings', 'emb_cost')){                
                $table->decimal('emb_cost', 12, 4)->default(0.0000)->after('print_cost');
            }
            if(!Schema::hasColumn('style_costings', 'wash_cost')){                
                $table->decimal('wash_cost', 12, 4)->default(0.0000)->after('emb_cost');
            }
            if(!Schema::hasColumn('style_costings', 'cm_cost')){                
                $table->decimal('cm_cost', 12, 4)->default(0.0000)->after('wash_cost');
            }
            if(!Schema::hasColumn('style_costings', 'overhead_cost')){                
                $table->decimal('overhead_cost', 12, 4)->default(0.0000)->after('cm_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('style_costings', function (Blueprint $table) {
            if(Schema::hasColumn('style_costings', 'print_cost')){
                $table->dropColumn('print_cost');                
            }
            if(Schema::hasColumn('style_costings', 'emb_cost')){
                $table->dropColumn('emb_cost');                
            }
            if(Schema::hasColumn('style_costings', 'wash_cost')){
                $table->dropColumn('wash_cost');                
            }
            if(Schema::hasColumn('style_costings', 'cm_cost')){
                $table->dropColumn('cm_cost');                
            }
            if(Schema::hasColumn('style_costings', 'overhead_cost')){
                $table->dropColumn('overhead_cost');                
            }
        });
    }
};