<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Style extends Model
{
    protected $fillable = ['name', 'code', 'status'];

    // একটি স্টাইলের আন্ডারে অনেকগুলো আইটেম/ম্যাটেরিয়াল থাকতে পারে
    public function items(): HasMany {
        return $this->hasMany(Item::class);
    }

    protected static function booted()
    {
        static::creating(function ($style) {
            // যদি ম্যানুয়ালি কোনো কোড প্রোভাইড না করা হয়, তবে নাম থেকে অটো জেনারেট হবে
            if (empty($style->code)) {
                $generatedCode = Str::upper(Str::slug($style->name, '-'));
                
                // ডুপ্লিকেট এড়ানোর জন্য ইউনিকনেস চেক
                $originalCode = $generatedCode;
                $count = 1;
                
                while (static::where('code', $generatedCode)->exists()) {
                    $generatedCode = $originalCode . '-' . $count;
                    $count++;
                }
                
                $style->code = $generatedCode;
            }
        });
    }
}
