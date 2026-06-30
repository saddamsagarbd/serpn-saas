<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // ১. ভেন্ডরের মেইন ড্যাশবোর্ড (Real-time Mockup View)
    Route::get('/', function () {
        return view('tenant.dashboard');
    });

    // ২. ডামি ইনভয়েস/ক্যাশ মেমো ভিউ
    Route::get('/invoice-demo', function () {
        return view('tenant.invoice');
    });
});
