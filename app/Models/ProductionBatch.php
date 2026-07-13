<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionBatch extends Model
{
    protected $fillable = ['item_id', 'batch_no', 'production_date', 'quantity', 'barcode_start', 'barcode_end'];

    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
}
