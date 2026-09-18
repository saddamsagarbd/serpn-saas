<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedNoteItem extends Model
{
    protected $guarded = [];

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ColorContext::class, 'color_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class, 'size_id');
    }

    public function goodsReceivedNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'item_id', 'item_id');
    }
}