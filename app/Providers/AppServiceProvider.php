<?php

namespace App\Providers;

use App\Models\User;
use App\Notifications\TenantCredentialsNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\DatabaseMigrated;
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
        ini_set('max_execution_time', 120); // ডাটাবেজ তৈরি ও মাইগ্রেশনের জন্য সেফটি টাইম

        // Event ফাসাদ ব্যবহার করে TenantCreated ইভেন্টটি লিসেন করুন
        // Event::listen(TenantCreated::class, function (TenantCreated $event) {
        Event::listen(DatabaseMigrated::class, function ($event) {
            $tenant = $event->tenant;
            $defaultPassword = '12345678';

            // একটি ফ্ল্যাগ ভেরিয়েবল যা ট্র্যাক করবে ইউজারটি নতুন কি না
            $isNewUser = false;

            // ভেন্ডরের নিজস্ব ডাটাবেজ কনটেক্সটে সুইচ করে অটোমেটিক Admin User তৈরি করা
            $tenant->run(function () use ($tenant, $defaultPassword) {
                $user =User::updateOrCreate(
                    [
                        'email' => $tenant->owner_email
                    ],[
                    'name'     => $tenant->owner_name,
                    'phone'    => $tenant->owner_phone,
                    'password' => Hash::make($defaultPassword), // ডিফল্ট পাসওয়ার্ড
                    'role'     => 'admin', // ভেন্ডর প্যানেলের মেইন সুপার ইউজার
                ]);
                if ($user->wasRecentlyCreated) {
                    $isNewUser = true;
                }
            });

            // ২. মেইল পাঠানোর প্রসেসকে আলাদা ট্রাই-ক্যাচ-এ রাখা হলো যেন মেইল ফেইল করলেও ডাটাবেজ ডিলিট না হয়
            if ($isNewUser) {
                try {
                    $centralDomain = config('tenancy.central_domains')[0] ?? 'serpn-saas.test';
                    $loginUrl = 'http://' . $tenant->id . '.' . $centralDomain . '/login';

                    Notification::route('mail', $tenant->owner_email)
                        ->notify(new TenantCredentialsNotification($tenant, $defaultPassword, $loginUrl));
                        
                } catch (\Exception $mailException) {
                    // ইমেইল যদি কনফিগারেশনের কারণে ফেইল করে, তবে সেটি ব্যাকএন্ড লগে সেভ হবে, কিন্তু মূল প্রসেস থামবে না
                    Log::error('Tenant Mail Delivery Failed: ' . $mailException->getMessage());
                }

            }
        });
    }
}