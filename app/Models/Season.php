<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    public function style(): HasMany {
        return $this->hasMany(Style::class);
    }
}
