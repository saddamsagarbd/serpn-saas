<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Item;
use App\Models\LedgerEntry;
use App\Models\SalesInvoice;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(){
        $sales = SalesInvoice::with('customer')->latest()->get();
        return view('tenant.sale.index', compact('sales'));
    }
    public function pos(){
        return view('tenant.sale.pos');
    }
    public function salesInvoice(Request $request){
        return DB::transaction(function () use ($request) {
            // ১. সেলস ইনভয়েস ক্রিয়েট
            $invoice = SalesInvoice::create([
                'invoice_no' => $request->invoice_no, // e.g., H57/INV/Icon/11
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'challan_no' => $request->challan_no, // 3261
                'grand_total' => $request->grand_total,
            ]);

            // ২. ইনভেন্টরি স্টক মাইনাস
            foreach ($request->items as $item) {
                Item::where('id', $item['item_id'])->decrement('stock_qty', $item['qty']);
            }

            // 🔌 ACCOUNTS MODULE INTEGRATION: অটো পোস্টিং রেভিনিউ ভাউচার
            $voucher = Voucher::create([
                'voucher_no' => 'SV-' . $invoice->invoice_no,
                'date' => $invoice->invoice_date,
                'narration' => "Commercial Goods Sold via Invoice: " . $invoice->invoice_no . " Challan: " . $invoice->challan_no,
            ]);

            // ডেবিট হেড: Accounts Receivable (Asset) বা Cash Box
            $receivableAccount = ChartOfAccount::where('code', '1001')->orWhere('name', 'like', '%Cash%')->first();
            // ক্রেডিট হেড: Sales Income (Revenue)
            $salesIncomeAccount = ChartOfAccount::where('code', '4001')->orWhere('name', 'like', '%Sales%')->first();

            if ($receivableAccount && $salesIncomeAccount) {
                // Debit (Asset/Cash flow Up)
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $receivableAccount->id,
                    'debit' => $invoice->grand_total,
                    'credit' => 0
                ]);

                // Credit (Income Up)
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $salesIncomeAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->grand_total
                ]);
            }

            return redirect()->route('tenant.sales.sales')->with('success', 'Sales Invoice saved and General Ledger Balance updated.');
        });
    }
    public function salesReturn(){
        return view('tenant.sale.sales-return');
    }
    public function customers(){
        return view('tenant.customer.index');
    }

}

