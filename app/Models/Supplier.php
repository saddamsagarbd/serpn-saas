<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Supplier extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier) {
            if (empty($supplier->supplier_code)) {
                $supplier->supplier_code = static::generateCode();
            }
        });
    }

    /** Generates a code like SUP-9THNT, matching the design's placeholder format. */
    public static function generateCode(): string
    {
        do {
            $code = 'SUP-' . strtoupper(Str::random(5));
        } while (static::where('supplier_code', $code)->exists());
        return $code;
    }


    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function voucherLines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }

    /** Net amount currently owed TO this supplier. */
    public function outstandingBalance(): float
    {
        return (float) $this->voucherLines()
            ->whereHas('voucher', fn ($q) => $q->where('status', 'posted'))
            ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->value('balance');
    }

}