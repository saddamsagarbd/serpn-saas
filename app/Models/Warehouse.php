<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($warehouse) {
            $nextId = DB::table('warehouses')->max('id') + 1;
            $warehouse->code = 'WH-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        });
    }
}
