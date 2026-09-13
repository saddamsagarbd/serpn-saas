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
        Schema::table('goods_received_note_items', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_received_note_items', 'style_id')) {
                $table->foreignId('style_id')->nullable()->after('item_id')->constrained('styles')->nullOnDelete();
            }
            if (!Schema::hasColumn('goods_received_note_items', 'color_id')) {
                $table->foreignId('color_id')->nullable()->after('style_id')->constrained('color_contexts')->nullOnDelete();
            }
            if (!Schema::hasColumn('goods_received_note_items', 'size_id')) {
                $table->foreignId('size_id')->nullable()->after('color_id')->constrained('size_charts')->nullOnDelete();
            }
            if (!Schema::hasColumn('goods_received_note_items', 'accepted_qty')) {
                $table->decimal('accepted_qty', 15, 2)->default(0)->after('rejected_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_received_note_items', function (Blueprint $table) {
            if (Schema::hasColumn('goods_received_note_items', 'style_id')) {
                $table->dropForeign(['style_id']);
                $table->dropColumn('style_id');
            }

            if (Schema::hasColumn('goods_received_note_items', 'color_id')) {
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }

            if (Schema::hasColumn('goods_received_note_items', 'size_id')) {
                $table->dropForeign(['size_id']);
                $table->dropColumn('size_id');
            }

            if (Schema::hasColumn('goods_received_note_items', 'accepted_qty')) {
                $table->dropColumn('accepted_qty');
            }
        });
    }
};
