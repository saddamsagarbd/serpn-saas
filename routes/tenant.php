<?php

declare(strict_types=1);

use App\Livewire\Tenant\Dashboard as TenantDashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
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
    Route::get('/dashboard', TenantDashboard::class)->name('tenant.dashboard');

    Route::get('/login', function() {
        return view('livewire.pages.auth.login'); 
    })->name('login');
});