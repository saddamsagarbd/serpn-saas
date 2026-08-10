<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderItem extends Model
{
    protected $guarded = [];

    public function style(): BelongsTo 
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function costing(): HasMany {
        return $this->hasMany(StyleCosting::class, 'style_id', 'style_id');
    }
    
}