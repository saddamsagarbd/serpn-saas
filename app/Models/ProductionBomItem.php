<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionBomItem extends Model
{
    protected $guarded = [];

    public function productionBom(): BelongsTo
    {
        return $this->belongsTo(ProductionBom::class);
    }

    public function bomItem(): BelongsTo
    {
        return $this->belongsTo(BomItem::class, 'bom_item_id');
    }

    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class, 'sales_order_item_id');
    }
}
