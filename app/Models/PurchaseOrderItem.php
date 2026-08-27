<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $guarded = [];

    public function item(): BelongsTo {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    // Color Relation
    public function color(): BelongsTo 
    {
        return $this->belongsTo(ColorContext::class, 'color_id');
    }

    // Size Relation
    public function size(): BelongsTo 
    {
        return $this->belongsTo(SizeChart::class, 'size_id');
    }

    // Unit Relation
    public function unit(): BelongsTo 
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}