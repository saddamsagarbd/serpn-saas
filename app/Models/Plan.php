<?php

namespace App\Models;

use App\Models\Concerns\HasFeatures;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFeatures;
    protected $fillable = [
        'code', 'title', 'price', 'currency', 
        'billing_interval', 'billing_period', 
        'max_invoice_limit', 'max_product_limit', 
        'features', 'is_active'
    ];

    // JSON ডেটাকে লারাভেলে অটোমেটিক অ্যারেতে রূপান্তর করার জন্য
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean'
    ];

    // একটি প্ল্যানের অধীনে অনেক টেন্যান্ট থাকতে পারে
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}