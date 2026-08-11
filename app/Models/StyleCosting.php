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
        // $this->update([
        //     'total_rm_cost' => $this->bomItems()->sum('total_cost')
        // ]);
        $totalRmCost = (float) $this->bomItems()->sum('total_cost');

        $this->total_rm_cost = $totalRmCost;
        $this->save();

        // এরপর সব নির্ভরতা (Base cost, FOB) আপডেট করা
        $this->recalculateCosting();
    }

    /**
     * Recalculate all costs, markups, taxes, and calculated FOB price.
     *
     * @return void
     */
    public function recalculateCosting(): void
    {
        // ১. BOM Items থেকে মোট Raw Material Cost (RM) এর যোগফল
        $totalRmCost = (float) $this->bomItems()->sum('total_cost');

        // ২. Base Cost = RM Cost + Total Service Cost (CM, Wash, Print, Overhead)
        $totalServiceCost = (float) ($this->total_service_cost ?? 0);
        $baseCost = $totalRmCost + $totalServiceCost;

        // ৩. Markups Percentages
        $revenuePercent = (float) ($this->revenue_percent ?? 0);
        $aitPercent     = (float) ($this->ait_percent ?? 0);
        $vatPercent     = (float) ($this->vat_percent ?? 0);

        // ৪. Amounts Calculation (Sequence: Base -> Revenue -> AIT -> VAT)
        $revenueAmount = $baseCost * ($revenuePercent / 100);
        
        // AIT হিসেব (Base Cost + Revenue-এর ওপর)
        $subtotalForAit = $baseCost + $revenueAmount;
        $aitAmount      = $subtotalForAit * ($aitPercent / 100);

        // VAT হিসেব (Base Cost + Revenue + AIT-এর ওপর)
        $subtotalForVat = $subtotalForAit + $aitAmount;
        $vatAmount      = $subtotalForVat * ($vatPercent / 100);

        // ৫. Final Calculated Net FOB Price
        $calculatedFob = $baseCost + $revenueAmount + $aitAmount + $vatAmount;

        // ৬. ডাটাবেজে আপডেট করা (Precision 4 decimal point ধরে)
        $this->update([
            'total_rm_cost'   => round($totalRmCost, 4),
            'base_cost'       => round($baseCost, 4),
            'revenue_amount'  => round($revenueAmount, 4),
            'ait_amount'      => round($aitAmount, 4),
            'vat_amount'      => round($vatAmount, 4),
            'calculated_fob'  => round($calculatedFob, 4),
        ]);
    }
}
