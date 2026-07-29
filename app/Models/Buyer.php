<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Country;

class Buyer extends Model
{
    protected $guarded = [];

    public function style(): HasMany {
        return $this->hasMany(Style::class);
    }

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
