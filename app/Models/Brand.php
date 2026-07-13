<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'code', 'status'];

    // ১. এই ব্র্যান্ডটি যদি কোনো ব্র্যান্ডের সাব-ব্র্যান্ড হয় (Parent Relation)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'parent_id');
    }

    // ২. এই ব্র্যান্ডের আন্ডারে যতগুলো সাব-ব্র্যান্ড আছে (Child Relation)
    public function subBrands(): HasMany
    {
        return $this->hasMany(Brand::class, 'parent_id');
    }

    // ⚡ আগের মতোই Eloquent Boot Method দিয়ে অটো-ইউনিক কোড জেনারেটর
    protected static function booted()
    {
        static::creating(function ($brand) {
            if (empty($brand->code)) {
                $generatedCode = Str::upper(Str::slug($brand->name, '-'));
                $originalCode = $generatedCode;
                $count = 1;
                while (static::where('code', $generatedCode)->exists()) {
                    $generatedCode = $originalCode . '-' . $count;
                    $count++;
                }
                $brand->code = $generatedCode;
            }
        });
    }
}
