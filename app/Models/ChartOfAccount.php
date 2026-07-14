<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'parent_id',
        'opening_balance',
        'is_bank_account', 'is_control_account', 'is_system_defined', 'status',
    ];

    protected $casts = [
        'opening_balance'     => 'decimal:2',
        'is_bank_account'     => 'boolean',
        'is_control_account'  => 'boolean',
        'is_system_defined'   => 'boolean',
    ];

    public const DEBIT_NORMAL  = ['asset', 'expense'];
    public const CREDIT_NORMAL = ['liability', 'equity', 'income'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }
 
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }
 
    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class);
    }
 
    public function voucherLines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }

    // ---- Scopes ----
    
    public function scopeOfType(Builder $query, string $type): Builder { return $query->where('type', $type); }
    public function scopeAssets(Builder $query): Builder { return $query->where('type', 'asset'); }
    public function scopeLiabilities(Builder $query): Builder { return $query->where('type', 'liability'); }
    public function scopeEquity(Builder $query): Builder { return $query->where('type', 'equity'); }
    public function scopeIncomeAccounts(Builder $query): Builder { return $query->where('type', 'income'); }
    public function scopeExpenseAccounts(Builder $query): Builder { return $query->where('type', 'expense'); }
    public function scopeBankAccounts(Builder $query): Builder { return $query->where('is_bank_account', true); }
    public function scopeActive(Builder $query): Builder { return $query->where('status', 'active'); }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function isDebitNormal(): bool
    {
        return in_array($this->type, self::DEBIT_NORMAL);
    }

    public function balance(?string $asOfDate = null): float
    {
        $query = $this->voucherLines()->whereHas('voucher', function ($q) use ($asOfDate) {
            $q->where('status', 'posted');
            if ($asOfDate) $q->where('date', '<=', $asOfDate);
        });

        $totals = $query->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')->first();

        $movement = $this->isDebitNormal()
            ? $totals->total_debit - $totals->total_credit
            : $totals->total_credit - $totals->total_debit;

        return (float) ($this->opening_balance + $movement);
    }


}