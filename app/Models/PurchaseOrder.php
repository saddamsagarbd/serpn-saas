<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    public function supplier(): BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function order(): HasMany {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}