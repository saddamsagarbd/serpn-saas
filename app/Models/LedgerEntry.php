<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = ['voucher_id', 'chart_of_account_id', 'debit', 'credit'];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
    
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}