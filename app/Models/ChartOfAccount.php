<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'parent_id',
        'opening_balance',
        'status',
        'is_system_defined'
    ];

    // রিলেশন: এই হেডের আন্ডারে কী কী সাব-হেড/চাইল্ড অ্যাকাউন্ট আছে
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')->with('children');
    }

    // রিলেশন: এই হেডের মূল মেইন প্যারেন্ট হেড কোনটি
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }
}