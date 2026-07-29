<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    protected $guarded = [];

    /**
     * Get the parent costing sheet context owning this BOM line item.
     */
    public function styleCosting(): BelongsTo
    {
        return $this->belongsTo(StyleCosting::class, 'style_costing_id');
    }
}
