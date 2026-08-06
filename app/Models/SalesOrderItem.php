<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderItem extends Model
{
    protected $guarded = [];

    public function costing(): HasMany {
        return $this->hasMany(StyleCosting::class, 'style_id');
    }
    
}