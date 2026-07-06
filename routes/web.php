<?php

use App\Http\Controllers\PlanController;
use App\Http\Controllers\TenantController;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;
use Illuminate\Http\Request;

foreach (config('tenancy.central_domains') as $domain) {
    Route::group(['domain' => $domain], function () {

        // 1. landing Page
        Route::get('/', function () {
            return view('welcome');
        });

        // 2. Super Admin Protected routes (no one can access without authentication)
        Route::middleware(['auth', 'verified'])->group(function () {
            
            // Super Admin Dashboard
            Route::get('/dashboard', function () {
                $tenants = Tenant::all(); // with('domains') সাময়িকভাবে বাদ দেওয়া হলো রিলেশন এরর এড়াতে
                return view('dashboard', compact('tenants'));
            })->name('dashboard');

            // Super Admin Profile
            Route::get('/profile', function () {
                return 'Profile Page Mockup';
            })->name('profile');

            // Plan List
            Route::get('/plans', [PlanController::class, 'index'])->name('plans');

            // New Plan Add (POST Request)
            Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');

            // Tenant List
            Route::get('/tenants', [TenantController::class, 'index'])->name('tenants');

            // New Tenant Add (POST Request)
            Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');

            Route::post('/logout', function (Request $request, Logout $logout) {
                $logout();

                return redirect()->route('login');
            })->name('logout');

        });

        // ব্রীজের ডিফল্ট লগইন/রেজিস্ট্রেশন রুটস
        require __DIR__.'/auth.php';
    });
}