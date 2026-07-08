<?php

declare(strict_types=1);

use App\Livewire\Actions\Logout;
use App\Livewire\Tenant\Dashboard as TenantDashboard;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Livewire\Volt\Volt;
use App\Http\Controllers\Tenant\{
    DashboardController,
    ProfileController,
    SettingController,
    InventoryController,
    SalesController,
    PurchaseController,
    AccountController,
    HrmController,
    CrmController,
    WebsiteController,
    SmsController,
    ReportController,
    WarehouseController,
};

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

Route::domain('{tenant}.serpn-saas.test')
    ->middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
    ])->group(function () {
        Route::get('/dashboard', TenantDashboard::class)->name('tenant.dashboard');

        Route::get('/', function() {
            return redirect()->route('tenant.login');
        });

        Volt::route('/login', 'pages.auth.login')->name('tenant.login');

        Route::post('/logout', function (Request $request, Logout $logout) {
            $logout();
            return redirect()->route('tenant.login');
        })->name('tenant.logout');

        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::get('settings', [SettingController::class, 'index'])->name('settings');

        // ---- Inventory ----
        Route::prefix('inventory')->name('inventory.')->middleware('feature:inventory')->group(function () {
            Route::resource('products', InventoryController::class)->parameters(['products' => 'product']);
            Route::get('categories', [InventoryController::class, 'categories'])->name('categories');
            Route::get('brands', [InventoryController::class, 'brands'])->name('brands');
            Route::get('units', [InventoryController::class, 'units'])->name('units');
            Route::get('stock', [InventoryController::class, 'stock'])->name('stock');
            Route::get('stock-adjustment', [InventoryController::class, 'stockAdjustment'])->name('stock-adjustment');
            Route::get('barcode', [InventoryController::class, 'barcode'])->name('barcode');
            Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        });

        // ---- Sales ----
        Route::prefix('sales')->name('sales.')->middleware('feature:sales')->group(function () {
            Route::get('pos', [SalesController::class, 'pos'])->name('pos');
            Route::get('sales', [SalesController::class, 'index'])->name('sales');
            Route::get('customers', [SalesController::class, 'customers'])->name('customers');
            Route::get('sales-return', [SalesController::class, 'salesReturn'])->name('sales-return');
            Route::get('quotation', [SalesController::class, 'quotation'])->name('quotation');
        });

        // ---- Purchase ----
        Route::prefix('purchase')->name('purchase.')->middleware('feature:purchase')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('purchase');
            Route::get('/purchase-form', [PurchaseController::class, 'purchaseForm'])->name('form');
            Route::post('/po-create', [PurchaseController::class, 'poCreate'])->name('store');
            Route::get('/grn', [PurchaseController::class, 'goodsReceivedNotes'])->name('grn');
            Route::post('/grn-transaction', [PurchaseController::class, 'saveGRNTransaction'])->name('grn.store');
            Route::get('/suppliers', [PurchaseController::class, 'suppliers'])->name('suppliers');
            Route::get('/suppliers-form', [PurchaseController::class, 'suppliersForm'])->name('suppliers.form');
            Route::get('/purchase-return', [PurchaseController::class, 'purchaseReturn'])->name('purchase-return');
        });

        // ---- Accounts ----
        Route::prefix('accounts')->name('accounts.')->middleware('feature:accounts')->group(function () {
            Route::get('dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
            Route::get('income', [AccountController::class, 'income'])->name('income');
            Route::get('expense', [AccountController::class, 'expense'])->name('expense');
            Route::get('transactions', [AccountController::class, 'transactions'])->name('transactions');
            Route::get('chart-of-accounts', [AccountController::class, 'chartOfAccounts'])->name('chart-of-accounts');
            Route::get('cash-book', [AccountController::class, 'cashBook'])->name('cash-book');
            Route::get('bank-accounts', [AccountController::class, 'bankAccounts'])->name('bank-accounts');
            Route::get('ledger', [AccountController::class, 'ledger'])->name('ledger');
            Route::get('journal-entry', [AccountController::class, 'journalEntry'])->name('journal-entry');
            Route::get('trial-balance', [AccountController::class, 'trialBalance'])->name('trial-balance');
            Route::get('profit-loss', [AccountController::class, 'profitLoss'])->name('profit-loss');
            Route::get('balance-sheet', [AccountController::class, 'balanceSheet'])->name('balance-sheet');
        });

        // ---- HRM ----
        Route::prefix('hrm')->name('hrm.')->middleware('feature:hrm')->group(function () {
            Route::get('employees', [HrmController::class, 'employees'])->name('employees');
            Route::get('departments', [HrmController::class, 'departments'])->name('departments');
            Route::get('designation', [HrmController::class, 'designation'])->name('designation');
            Route::get('attendance', [HrmController::class, 'attendance'])->name('attendance');
            Route::get('leave', [HrmController::class, 'leave'])->name('leave');
            Route::get('payroll', [HrmController::class, 'payroll'])->name('payroll');
        });
 
        // ---- CRM ----
        Route::prefix('crm')->name('crm.')->middleware('feature:crm')->group(function () {
            Route::get('customers', [CrmController::class, 'customers'])->name('customers');
            Route::get('leads', [CrmController::class, 'leads'])->name('leads');
            Route::get('follow-up', [CrmController::class, 'followUp'])->name('follow-up');
        });

        // ---- Website ----
        Route::prefix('website')->name('website.')->middleware('feature:website')->group(function () {
            Route::get('website', [WebsiteController::class, 'index'])->name('website');
            Route::get('pages', [WebsiteController::class, 'pages'])->name('pages');
            Route::get('blogs', [WebsiteController::class, 'blogs'])->name('blogs');
        });
 
        // ---- SMS ----
        Route::prefix('sms')->name('sms.')->middleware('feature:sms')->group(function () {
            Route::get('sms', [SmsController::class, 'index'])->name('sms');
            Route::get('templates', [SmsController::class, 'templates'])->name('templates');
        });

        // ---- Reports ----
        Route::prefix('reports')->name('reports.')->middleware('feature:reports')->group(function () {
            Route::get('sales-report', [ReportController::class, 'salesReport'])->name('sales-report');
            Route::get('purchase-report', [ReportController::class, 'purchaseReport'])->name('purchase-report');
            Route::get('stock-report', [ReportController::class, 'stockReport'])->name('stock-report');
            Route::get('income-report', [ReportController::class, 'incomeReport'])->name('income-report');
            Route::get('expense-report', [ReportController::class, 'expenseReport'])->name('expense-report');
            Route::get('customer-report', [ReportController::class, 'customerReport'])->name('customer-report');
        });
    });