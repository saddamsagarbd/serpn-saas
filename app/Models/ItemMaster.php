<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemMaster extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function batches(): HasMany { return $this->hasMany(ProductionBatch::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
