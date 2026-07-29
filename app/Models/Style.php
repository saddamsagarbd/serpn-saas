<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Style extends Model
{
    protected $fillable = [
        'tenant_id', 'style_code', 'style_name', 'buyer_id', 'season_id', 'target_price', 'image'
    ];

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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
            }
            // যদি ম্যানুয়ালি কোনো কোড প্রোভাইড না করা হয়, তবে নাম থেকে অটো জেনারেট হবে
            if (empty($model->style_code)) {
                $generatedCode = Str::upper(Str::slug($model->style_name, '-'));
                
                $originalCode = $generatedCode;
                $count = 1;
                
                // ফিক্সড: কলামের নাম 'style_code' করা হয়েছে এবং কারেন্ট tenant_id স্কোপ যোগ করা হয়েছে
                while (
                    static::where('style_code', $generatedCode)
                        ->when($model->tenant_id, function ($query) use ($model) {
                            return $query->where('tenant_id', $model->tenant_id);
                        })
                        ->exists()
                ) {
                    $generatedCode = $originalCode . '-' . $count;
                    $count++;
                }
                
                $model->style_code = $generatedCode;
            }
        });
    }
}
