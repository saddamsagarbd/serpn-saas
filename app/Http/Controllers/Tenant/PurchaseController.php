<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\GoodsReceivedNote;
use App\Models\Item;
use App\Models\LedgerEntry;
use App\Models\PurchaseOrder;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index() {
        // $purchaseOrders = PurchaseOrder::with('supplier')->latest()->get();
        $purchaseOrders = [];
        return view('tenant.purchase.purchase-order', compact('purchaseOrders'));
    }
    public function purchaseForm() {
        return view('tenant.purchase.po-form');
    }
    public function poCreate(Request $request) {
        // এক্সেল ফাইলের কলাম অনুযায়ী ডাটা ভ্যালিডেশন
        $request->validate([
            'po_no' => 'required|unique:purchase_orders,po_no',
            'supplier_id' => 'required',
            'po_date' => 'required|date',
            'grand_total' => 'required|numeric'
        ]);

        return DB::transaction(function () use ($request) {
            // ১. পারচেজ অর্ডার বা ফেব্রিক বুকিং রেকর্ড তৈরি
            $po = PurchaseOrder::create([
                'po_no' => $request->po_no, // e.g., FBN-236/26-1 or 2664
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'grand_total' => $request->grand_total,
            ]);

            // ২. আইটেম ওয়াইজ লুপ চালিয়ে ইনভেন্টরি স্টক ও প্রাইস আপডেট (GRN/MRR এ সাধারণত স্টক ইন হয়, PO তে ট্র্যাকিং থাকে)
            foreach ($request->items as $item) {
                $inventoryItem = Item::find($item['item_id']);
                if ($inventoryItem) {
                    $inventoryItem->increment('stock_qty', $item['qty']);
                    $inventoryItem->update(['purchase_price' => $item['unit_amount']]);
                }
            }

            // 🔌 ACCOUNTS MODULE INTEGRATION: ডাবল-এন্ট্রি ভাউচার জেনারেট
            $voucher = Voucher::create([
                'voucher_no' => 'PV-' . $po->po_no,
                'date' => $po->po_date,
                'narration' => "Inventory Stock Purchased / Fabric Booked via PO No: " . $po->po_no,
            ]);

            // ডেবিট হেড: Inventory Asset / Raw Material Head (কোড অনুযায়ী সার্চ বা নির্দিষ্ট আইডি)
            $inventoryAccount = ChartOfAccount::where('code', '1002')->orWhere('name', 'like', '%Inventory%')->first();
            // ক্রেডিট হেড: Accounts Payable / Liability Head
            $payableAccount = ChartOfAccount::where('code', '2001')->orWhere('name', 'like', '%Payable%')->first();

            if ($inventoryAccount && $payableAccount) {
                // Debit Entry (Asset Increases)
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'debit' => $po->grand_total,
                    'credit' => 0
                ]);

                // Credit Entry (Liability Increases)
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $payableAccount->id,
                    'debit' => 0,
                    'credit' => $po->grand_total
                ]);
            }

            return redirect()->route('tenant.purchase.purchase')->with('success', 'Purchase Voucher Dynamic Entry Synced Successfully.');
        });
        return response()->json([]);
    }
    public function goodsReceivedNotes(){
        // $purchaseOrders = PurchaseOrder::where('status', 'Approved')->get();
        $purchaseOrders = [];
        return view('tenant.purchase.goods-received-notes', compact('purchaseOrders'));
    }
    public function saveGRNTransaction(Request $request){

        // আপনার GRN ফর্ম থেকে আসা ডাটা ভ্যালিডেশন
        $request->validate([
            'purchase_order_id' => 'required',
            'grn_date' => 'required|date',
            'items' => 'required|array'
        ]);

        return DB::transaction(function () use ($request) {
            $po = PurchaseOrder::findOrFail($request->purchase_order_id);
            
            // ক) GRN রেকর্ড সেভ করা
            $grn = GoodsReceivedNote::create([
                'purchase_order_id' => $po->id,
                'grn_no' => 'GRN-' . date('Ymd') . '-' . rand(100, 999),
                'received_date' => $request->grn_date,
                'total_amount' => $po->grand_total, // PO এর টোটাল ভ্যালু
            ]);

            // খ) ইনভেন্টরি স্টক প্লাস করা (আপনার inventory/index.blade.php এর ব্যাচ/স্টক মেকানিজমে সিঙ্ক হবে)
            foreach ($request->items as $incomingItem) {
                $item = Item::where('sku', $incomingItem['item_code'])
                            ->orWhere('id', $incomingItem['item_id'])
                            ->first();
                if ($item) {
                    // স্টক বৃদ্ধি করা (যা inventory.stock এ লাইভ রিফ্লেক্ট করবে)
                    $item->increment('stock_qty', $incomingItem['receiving_qty']);
                }
            }

            // গ) 🔌 ACCOUNTS DOUBLE ENTRY VOUCHER POSTING Engine
            // জার্নাল রুল: Raw Materials/Inventory Asset (Debit) এবং Accounts Payable Supplier (Credit)
            $voucher = Voucher::create([
                'voucher_no' => 'PV-' . $po->po_no,
                'date' => $request->grn_date,
                'narration' => "Material stock taken into warehouse via GRN against PO: " . $po->po_no,
            ]);

            // চার্ট অফ অ্যাকাউন্টস থেকে ডাইনামিক হেড খুঁজে নেওয়া
            $inventoryHead = ChartOfAccount::where('code', '1002')
                ->orWhere('name', 'like', '%Inventory%')
                ->orWhere('name', 'like', '%Stock%')
                ->first();
                
            $payableHead = ChartOfAccount::where('code', '2001')
                ->orWhere('name', 'like', '%Payable%')
                ->orWhere('name', 'like', '%Supplier%')
                ->first();

            if ($inventoryHead && $payableHead) {
                // Asset বেড়েছে -> Debit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $inventoryHead->id,
                    'debit' => $po->grand_total,
                    'credit' => 0
                ]);

                //Liability বেড়েছে -> Credit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $payableHead->id,
                    'debit' => 0,
                    'credit' => $po->grand_total
                ]);
            }

            // PO এবং GRN এর সাথে অ্যাকাউন্টস ভাউচার আইডি লিংক করে দেওয়া
            $po->update(['status' => 'Completed', 'voucher_id' => $voucher->id]);
            $grn->update(['voucher_id' => $voucher->id]);

            return redirect()->route('tenant.purchase.grn')->with('success', 'GRN Verified. Inventory Stock In & Accounting Ledger Successfully Balanced!');
        });

    }
    public function suppliers(){
        return view('tenant.supplier.index');
    }
    public function suppliersForm(){
        return view('tenant.supplier.supplier-form');
    }
}
