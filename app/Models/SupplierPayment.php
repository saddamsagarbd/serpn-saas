<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $guarded = [];

    public function supplier(): BelongsTo {
        return $this->belongsTo(Supplier::class, "supplier_id");        
    }
    
    public function bankAccount(): BelongsTo {
        return $this->belongsTo(ChartOfAccount::class, "bank_account_id");        
    }
    
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    
    public function items()
    {
        return $this->hasMany(SupplierPaymentItem::class, 'supplier_payment_id');
    }
}