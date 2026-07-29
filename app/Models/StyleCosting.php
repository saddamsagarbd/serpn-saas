<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StyleCosting extends Model
{
    protected $guarded = [];

    /**
     * Get the parent style that owns this costing sheet.
     */
    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    /**
     * Get the BOM items mapped inside this costing sheet instance.
     */
    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class, 'style_costing_id');
    }

    /**
     * Utility method to sync and update the absolute total raw material cost.
     * Useful to call after adding, editing, or removing items.
     */
    public function updateCalculatedTotalRmCost(): void
    {
        $this->update([
            'total_rm_cost' => $this->bomItems()->sum('total_cost')
        ]);
    }
}
