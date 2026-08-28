<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $guarded = [];

    public function itemVariant(): BelongsTo {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }
    public function warehouse() : BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }
}