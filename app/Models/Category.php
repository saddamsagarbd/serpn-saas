<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    // আলাদা ডেটাবেস হওয়ায় এখানে কোনো টেন্যান্ট ট্রেইট/স্কোপের প্রয়োজন নেই।
    
    protected $fillable = [
        'serial_number',
        'code',
        'name',
        'slug',
        'parent_id',
        'description',
        'is_active'
    ];

    /**
     * Get parent category inside current tenant database context
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get subcategories inside current tenant database context
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Stancl Multi-Database Safe Boot Logic
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // নাম থেকে স্লাগ জেনারেশন
            $category->slug = Str::slug($category->name);
            
            // কারেন্ট টেন্যান্টের ডেটাবেস থেকে সর্বশেষ ক্যাটাগরি রো চেক করা হচ্ছে
            $latestCategory = self::latest('id')->first();
            $nextSerial = $latestCategory ? ($latestCategory->serial_number + 1) : 101;
            
            $category->serial_number = $nextSerial;
            $category->code = 'CAT-' . $nextSerial;
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
