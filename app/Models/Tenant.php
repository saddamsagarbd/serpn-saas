<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;

    /**
     * The attributes that aren't mass assignable.
     * সেন্ট্রাল ডাটাবেজে টেন্যান্ট তৈরি করার সময় আপনার ফর্মের কাস্টম ফিল্ডগুলো
     * যাতে কোনো বাধা ছাড়াই সেভ হতে পারে, সেজন্য এটিকে গার্ড-ফ্রি রাখা হলো।
     */
    protected $guarded = [];

    /**
     * কাস্টম কলামের ডেটা কাস্টিং (ঐচ্ছিক কিন্তু স্ট্যান্ডার্ড)
     */
    protected $casts = [
        'plan_id' => 'integer',
    ];

    /**
     * প্যাকেজের অফিশিয়াল নিয়ম অনুযায়ী ডাটাবেজের কলামের ডেটা টাইপ 
     * বা ইন্টারনাল কাস্টম এট্রিবিউট রিটার্ন করার জন্য এই মেথডটি ব্যবহৃত হয়।
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'plan_id',
            'company_name',
            'owner_name',
            'owner_email',
            'owner_phone',
            'business_type'
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Check whether this tenant's plan has a given feature/module enabled.
     * Delegates to Plan::hasFeature() since features live on the plan,
     * not on the tenant itself.
     */
    public function hasFeature(string $key): bool
    {
        return $this->plan?->hasFeature($key) ?? false;
    }
 
    public function hasFeatures(array $keys): bool
    {
        return $this->plan?->hasFeatures($keys) ?? false;
    }
 
    public function hasAnyFeature(array $keys): bool
    {
        return $this->plan?->hasAnyFeature($keys) ?? false;
    }
}