<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['voucher_no', 'type', 'date', 'narration', 'total_amount'];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}