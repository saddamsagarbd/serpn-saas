<?php

declare(strict_types=1);

use App\Livewire\Tenant\Dashboard as TenantDashboard;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Livewire\Volt\Volt;
use App\Http\Controllers\Tenant\{
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
    CategoryController,
    ColorContextController,
    StyleController,
    UnitController,
    VoucherController,
    FebricController
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

        Volt::route('/login', 'pages.auth.login')->name('tenant.login');

        Route::post('/logout', [TenantDashboard::class, 'logout'])->name('tenant.logout');

        Route::redirect('/', '/login');

        Route::middleware(['tenant.auth'])->name('tenant.')->group(function () {
            Route::get('/dashboard', TenantDashboard::class)->name('dashboard');

            Route::get('profile', [ProfileController::class, 'index'])->name('profile');
            Route::get('settings', [SettingController::class, 'index'])->name('settings');

            // ---- Inventory ----
            Route::prefix('inventory')->name('inventory.')->middleware('feature:inventory')->group(function () {
                // Category Master
                Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
                Route::post('categories/delete/{id}', [CategoryController::class, 'delete'])->name('categories.destroy');

                // UOM
                Route::get('units', [UnitController::class, 'index'])->name('units');                
                Route::post('units/store', [UnitController::class, 'store'])->name('units.store');
                Route::put('units/update/{id}', [UnitController::class, 'update'])->name('units.update');
                Route::post('units/delete/{id}', [UnitController::class, 'delete'])->name('units.destroy');


                // Style
                Route::get('styles', [StyleController::class, 'index'])->name('styles');
                Route::post('styles/store', [StyleController::class, 'styleStore'])->name('styles.store');
                Route::put('styles/update/{id}', [StyleController::class, 'update'])->name('styles.update');
                Route::post('styles/delete/{id}', [StyleController::class, 'delete'])->name('styles.destroy');

                // Febric Spec
                Route::get('fabrics', [FebricController::class, 'index'])->name('fabrics');
                Route::post('fabrics/store', [FebricController::class, 'styleStore'])->name('fabrics.store');
                Route::put('fabrics/update/{id}', [FebricController::class, 'update'])->name('fabrics.update');

                // Color Context
                Route::get('color-contexts', [ColorContextController::class, 'index'])->name('color');
                Route::post('color-contexts/store', [ColorContextController::class, 'colorStore'])->name('color.store');
                Route::put('color-contexts/update/{id}', [ColorContextController::class, 'update'])->name('color.update');
                
                // Brands


                // Item Master
                Route::resource('items', InventoryController::class)->parameters(['items' => 'item']);
                Route::get('/items/create', [InventoryController::class, 'itemCreate'])->name('item.create');
                Route::get('/items/store', [InventoryController::class, 'itemStore'])->name('item.store');
                
                Route::get('brands', [InventoryController::class, 'brands'])->name('brands');
                Route::get('stock', [InventoryController::class, 'stock'])->name('stock');
                Route::post('/batch/store', [InventoryController::class, 'storeBatchProduction'])->name('batch.store');
                Route::get('/stock-entry', [InventoryController::class, 'stockEntry'])->name('stock.entry');
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
                Route::get('chart-of-accounts', [AccountController::class, 'index'])->name('coa.index');
                Route::post('chart-of-accounts/store', [AccountController::class, 'storeCoa'])->name('coa.store');
                Route::put('chart-of-accounts/update/{id}', [AccountController::class, 'updateCoa'])->name('coa.update');
                
                Route::get('income', [AccountController::class, 'income'])->name('income');
                Route::get('expense', [AccountController::class, 'expense'])->name('expense');
                Route::post('vouchers/store', [VoucherController::class, 'store'])->name('vouchers.store');
                
                Route::get('transactions', [AccountController::class, 'transactions'])->name('transactions');
                Route::get('ledger', [AccountController::class, 'ledger'])->name('ledger');
                Route::get('cash-book', [AccountController::class, 'cashBook'])->name('cash-book');
                Route::get('bank-accounts', [AccountController::class, 'bankAccounts'])->name('bank-accounts');
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
    });