<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Style extends Model
{
    protected $guarded = [];

    /**
     * Get the costing record associated with the style.
     */
    public function costing(): HasOne
    {
        return $this->hasOne(StyleCosting::class, 'style_id');
    }

    /**
     * Get all BOM items associated with the style through StyleCosting.
     */
    public function bomItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            BomItem::class,      // Target Model
            StyleCosting::class, // Intermediate Model
            'style_id',          // Foreign key on StyleCosting table...
            'style_costing_id',  // Foreign key on BomItem table...
            'id',                // Local key on Style table...
            'id'                 // Local key on StyleCosting table...
        );
    }

    // একটি স্টাইলের আন্ডারে অনেকগুলো আইটেম/ম্যাটেরিয়াল থাকতে পারে
    public function items(): HasMany {
        return $this->hasMany(Item::class);
    }

    public function buyer(): BelongsTo {
        return $this->belongsTo(Buyer::class);
    }

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function mprs()
    {
        // আপনার MPR বা Order মডেলে style_id ফরেইন কি (Foreign Key) থাকলে
        return $this->hasMany(SalesOrderItem::class, 'style_id'); 
    }

    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (session()->has('tenant_id')) {
    //             $model->tenant_id = session()->get('tenant_id');
    //         }
    //         // যদি ম্যানুয়ালি কোনো কোড প্রোভাইড না করা হয়, তবে নাম থেকে অটো জেনারেট হবে
    //         if (empty($model->style_code)) {
    //             $generatedCode = Str::upper(Str::slug($model->style_name, '-'));
                
    //             $originalCode = $generatedCode;
    //             $count = 1;
                
    //             // ফিক্সড: কলামের নাম 'style_code' করা হয়েছে এবং কারেন্ট tenant_id স্কোপ যোগ করা হয়েছে
    //             while (
    //                 static::where('style_number', $generatedCode)
    //                     ->when($model->tenant_id, function ($query) use ($model) {
    //                         return $query->where('tenant_id', $model->tenant_id);
    //                     })
    //                     ->exists()
    //             ) {
    //                 $generatedCode = $originalCode . '-' . $count;
    //                 $count++;
    //             }
                
    //             $model->style_code = $generatedCode;
    //         }
    //     });
    // }
}