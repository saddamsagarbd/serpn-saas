<?php

declare(strict_types=1);

use App\Livewire\Tenant\Dashboard as TenantDashboard;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Livewire\Volt\Volt;

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
    Route::get('/dashboard', TenantDashboard::class)->name('tenant.dashboard');

    Route::get('/', function() {
        return redirect()->route('tenant.login');
    });

    Volt::route('/login', 'pages.auth.login')->name('tenant.login');
});