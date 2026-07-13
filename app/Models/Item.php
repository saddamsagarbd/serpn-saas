<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = ['category_id', 'unit_id', 'sku', 'style_id', 'fabric_code', 'name', 'color', 'brand', 'stock_qty', 'purchase_price', 'sale_price'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function batches(): HasMany { return $this->hasMany(ProductionBatch::class); }
}
