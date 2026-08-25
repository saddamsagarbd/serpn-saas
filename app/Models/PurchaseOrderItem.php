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
}