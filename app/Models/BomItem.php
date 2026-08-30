<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    protected $guarded = [];

    /**
     * Get the parent costing sheet context owning this BOM line item.
     */
    public function styleCosting(): BelongsTo
    {
        return $this->belongsTo(StyleCosting::class, 'style_costing_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ColorContext::class, 'color_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class, 'size_id');
    }
    
    public function itemMaster(): BelongsTo
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'item_id', 'item_id');
    }

    public function latestStock()
    {
        return $this->hasOne(Stock::class, 'item_id', 'item_id')->latestOfMany();
    }
}