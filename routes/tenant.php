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
    BuyerController,
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
    FebricController,
    MRPController,
    OrderController,
    PurchaseOrderController,
    PurchaseRequisitionController,
    SeasonController,
    SizeChartController,
    SupplierController
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

    dashboard > 

    style - description - order qty - order fob per pcs - order rcv date - order delivery - remarks
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

            Route::get('/api/item-masters/search', [InventoryController::class, 'searchApi'])->name('api.item_masters.search');

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

                // Sizes
                Route::get('sizes', [SizeChartController::class, 'index'])->name('sizes');                
                Route::post('sizes/store', [SizeChartController::class, 'store'])->name('sizes.store');
                Route::put('sizes/update/{id}', [SizeChartController::class, 'update'])->name('sizes.update');

                // Buyers Routes
                Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers');
                Route::post('/buyers/store', [BuyerController::class, 'store'])->name('buyers.store');
                Route::put('/buyers/{id}/update', [BuyerController::class, 'update'])->name('buyers.update');

                // Seasons Routes
                Route::get('/seasons', [SeasonController::class, 'index'])->name('seasons');
                Route::post('/seasons/store', [SeasonController::class, 'store'])->name('seasons.store');
                Route::put('/seasons/{id}/update', [SeasonController::class, 'update'])->name('seasons.update');

                // Febric Spec
                Route::get('fabrics', [FebricController::class, 'index'])->name('fabrics');
                Route::post('fabrics/store', [FebricController::class, 'styleStore'])->name('fabrics.store');
                Route::put('fabrics/update/{id}', [FebricController::class, 'update'])->name('fabrics.update');

                // Color Context
                Route::get('color-contexts', [ColorContextController::class, 'index'])->name('color');
                Route::post('color-contexts/store', [ColorContextController::class, 'colorStore'])->name('color.store');
                Route::put('color-contexts/update/{id}', [ColorContextController::class, 'update'])->name('color.update');
                
                // Manufacturing-only setup items
                Route::get('raw-materials', [ColorContextController::class, 'index'])->name('raw-materials');
                Route::get('bom', [ColorContextController::class, 'index'])->name('bom');
                // Brands
                Route::get('brands', [InventoryController::class, 'brands'])->name('brands');
                Route::post('brands/store', [InventoryController::class, 'brandStore'])->name('brands.store');
                Route::put('brands/update/{id}', [InventoryController::class, 'updateBrand'])->name('brands.update');

                // Item Master
                Route::post('/items/import', [InventoryController::class, 'importCsv'])->name('items.import');
                Route::get('/items/download-sample', [InventoryController::class, 'downloadSampleCsv'])->name('items.download-sample');
                Route::resource('items', InventoryController::class)->parameters(['items' => 'item'])->except(['edit', 'update', 'show']);
                Route::get('/items/create', [InventoryController::class, 'itemCreate'])->name('item.create');
                Route::post('/items/store', [InventoryController::class, 'itemStore'])->name('item.store');
                Route::get('/items/{id}/edit', [InventoryController::class, 'itemedit'])->name('item.edit');
                Route::put('/items/{id}', [InventoryController::class, 'itemupdate'])->name('item.update');
                
                Route::get('stock', [InventoryController::class, 'stock'])->name('stock');
                Route::post('stock/store', [StyleController::class, 'storeStock'])->name('stock.style-with-items-save');
                Route::post('/batch/store', [InventoryController::class, 'storeBatchProduction'])->name('batch.store');
                Route::get('/stock-entry', [InventoryController::class, 'stockEntry'])->name('stock.entry');
                Route::get('barcode', [InventoryController::class, 'barcode'])->name('barcode');
                Route::get('/stock/transfer', [WarehouseController::class, 'stockTransferT'])->name('stock.transfer');
                
                // Warehouse setup
                Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
                Route::post('/warehouses/store', [WarehouseController::class, 'store'])->name('warehouses.store');
                Route::put('/warehouses/{id}/update', [WarehouseController::class, 'update'])->name('warehouses.update');
            });

            // ---- Purchase ----
            Route::prefix('merchandising')->name('merch.')->middleware('feature:merchandising')->group(function () {
                // Style
                Route::get('styles', [StyleController::class, 'index'])->name('styles');
                Route::get('styles/create', [StyleController::class, 'createStyle'])->name('styles.create');
                Route::post('styles/store', [StyleController::class, 'styleStore'])->name('styles.store');
                Route::get('styles/{id}/edit', [StyleController::class, 'edit'])->name('styles.edit');
                Route::put('styles/update/{id}', [StyleController::class, 'update'])->name('styles.update');
                Route::get('styles/{id}/details', [StyleController::class, 'show'])->name('styles.show');
                Route::get('styles/{id}/export-pdf', [StyleController::class, 'exportPdf'])->name('styles.export-pdf');
                
                Route::get('/mrp-order', [MRPController::class, 'index'])->name('mrp.index');
                Route::get('/mrp-order-create', [MRPController::class, 'createMrpOrder'])->name('mrp.order-create');
                Route::post('/mrp-order-post', [MRPController::class, 'mrpOrderCreate'])->name('mrp.order-store');
                Route::get('/mrp-order-details/{id}', [MRPController::class, 'mrpOrderDetails'])->name('mrp.order-details');
                Route::get('/mrp-order/{id}/export-pdf', [MRPController::class, 'exportPdf'])->name('mrp-order-export-pdf');
                Route::get('/mrp-order-edit/{id}', [MRPController::class, 'mrpOrderEdit'])->name('mrp.order-edit');
                Route::put('/mrp-orders-update/{id}', [MRPController::class, 'update'])->name('mrp.orders-update');
                
            });
            Route::prefix('purchase')->name('purchase.')->middleware('feature:purchase')->group(function () {

                Route::get('/purchase-requisition', [PurchaseRequisitionController::class, 'index'])->name('pr.index');
                Route::get('/purchase-order', [PurchaseOrderController::class, 'index'])->name('po.index');

                Route::get('/grn', [PurchaseController::class, 'goodsReceivedNotes'])->name('grn.index');
                Route::post('/grn-transaction', [PurchaseController::class, 'saveGRNTransaction'])->name('grn.store');

                Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
                Route::get('/suppliers-form', [SupplierController::class, 'create'])->name('suppliers.form');
                Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
                Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
                Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');

                Route::get('/purchase-return', [PurchaseController::class, 'purchaseReturn'])->name('return');
                Route::get('/fabric-booking', [PurchaseController::class, 'fabricBooking'])->name('fabric-booking');
            });

            // ---- Sales ----
            Route::prefix('sales')->name('sales.')->middleware('feature:sales')->group(function () {
                Route::get('/orders', [SalesController::class, 'index'])->name('index');
                Route::get('pos', [SalesController::class, 'pos'])->name('pos');
                Route::get('export-invoice', [SalesController::class, 'exportInvoice'])->name('export-invoice');
                Route::get('customers', [SalesController::class, 'customers'])->name('customers');
                Route::get('sales-return', [SalesController::class, 'salesReturn'])->name('sales-return');
                Route::get('quotation', [SalesController::class, 'quotation'])->name('quotation');
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
                Route::get('piece-rate', [HrmController::class, 'pieceRate'])->name('piece-rate');
            });
    
            // ---- CRM ----
            Route::prefix('crm')->name('crm.')->middleware('feature:crm')->group(function () {
                Route::get('customers', [CrmController::class, 'customers'])->name('customers');
                Route::get('leads', [CrmController::class, 'leads'])->name('leads');
                Route::get('follow-up', [CrmController::class, 'followUp'])->name('follow-up');
                Route::get('site-visits', [CrmController::class, 'siteVisits'])->name('site-visits');
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
                Route::get('style-profitability', [ReportController::class, 'styleProfitabilityReport'])->name('style-profitability');
                Route::get('export-statement', [ReportController::class, 'exportStatement'])->name('export-statement');
                Route::get('rent-collection', [ReportController::class, 'rentCollection'])->name('rent-collection');
                Route::get('occupancy', [ReportController::class, 'occupancyReport'])->name('occupancy');
            });
        });
    });