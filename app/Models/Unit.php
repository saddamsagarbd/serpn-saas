<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'is_base_unit',
        'base_unit_id',
        'operator_value',
        'operator',
        'status'
    ];

    // রিলেশন: এই বেজ ইউনিটের অধীনে কী কী সাব-ইউনিট আছে
    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    // রিলেশন: এই সাব-ইউনিটের মূল বেজ ইউনিট কোনটি
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }
}