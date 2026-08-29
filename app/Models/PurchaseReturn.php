<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    protected $guarded = [];

    public function supplier(): BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }

    public function goodsReceivedNote(): BelongsTo {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function items(): HasMany 
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }
}
