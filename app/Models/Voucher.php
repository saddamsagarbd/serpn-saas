<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    // protected $fillable = ['voucher_no', 'type', 'date', 'narration', 'total_amount'];
    protected $guarded = [];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'voucher_id');
    }

    public function coaHead()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function paymentHead()
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_method_id');
    }
}