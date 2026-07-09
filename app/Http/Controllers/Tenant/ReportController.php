<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salesReport(Request $request) { return view('tenant.reports.sales-report'); }
    public function purchaseReport(Request $request) { return view('tenant.reports.purchase-report'); }
    public function stockReport(Request $request) { return view('tenant.reports.stock-report'); }
    public function incomeReport(Request $request) { return view('tenant.reports.income-report'); }
    public function expenseReport(Request $request) { return view('tenant.reports.expense-report'); }
    public function customerReport(Request $request) { return view('tenant.reports.customer-report'); }
}
