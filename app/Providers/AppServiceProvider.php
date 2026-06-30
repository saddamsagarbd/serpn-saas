<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenantCreated;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // যখনই সেন্ট্রাল প্যানেলে নতুন Tenant তৈরি শেষ হবে
        ini_set('max_execution_time', 120); // ডাটাবেজ তৈরি ও মাইগ্রেশনের জন্য সেফটি টাইম

        // ২. Event ফাসাদ ব্যবহার করে TenantCreated ইভেন্টটি লিসেন করুন
        Event::listen(TenantCreated::class, function (TenantCreated $event) {
            $tenant = $event->tenant;

            // ১. সাবডোমেইন কানেক্ট করা
            $centralDomain = config('tenancy.central_domains')[0] ?? 'saas-erp.test';
            $slug = strtolower(str_replace([" ", "-"], "", $tenant->company_name));
            $tenant->domains()->create([
                'domain' => $slug . '.' . $centralDomain
            ]);

            // ২. ভেন্ডরের নিজস্ব ডাটাবেজ কনটেক্সটে সুইচ করে অটোমেটিক Admin User তৈরি করা
            $tenant->run(function () use ($tenant) {
                User::create([
                    'name'     => $tenant->owner_name,
                    'email'    => $tenant->owner_email,
                    'phone'    => $tenant->owner_phone,
                    'password' => Hash::make('12345678'), // ডিফল্ট পাসওয়ার্ড
                    'role'     => 'admin', // ভেন্ডর প্যানেলের মেইন সুপার ইউজার
                ]);
            });
        });
    }
}
