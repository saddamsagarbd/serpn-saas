<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\LedgerEntry;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = SupplierInvoice::with(['supplier', 'purchaseOrder', 'grn', 'voucher'])
            ->where('tenant_id', tenant('id'))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return view('tenant.purchase.invoice.index', compact('invoices'));
    }
    // ১. GRN লোড করার API (3-Way Match Data Provider)
    public function getGrnData(Request $request)
    {
        $request->validate([
            'goods_received_note_id' => 'required|exists:goods_received_notes,id'
        ]);

        $grn = GoodsReceivedNote::with([
                'supplier', 
                'purchaseOrder', 
                'items.item', 
                'items.color', 
                'items.size', 
                'items.style'
            ])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->goods_received_note_id);

        $items = $grn->items->map(function ($item) use ($request) {
            $totalReturnedQty = (float) DB::table('purchase_return_items')
                ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->where('purchase_returns.goods_received_note_id', $request->goods_received_note_id)
                ->where('purchase_return_items.item_id', $item->item_id) // 💡 $item->item_id ব্যবহার করা নিরাপদ
                ->when($item->style_id, fn($q) => $q->where('purchase_return_items.style_id', $item->style_id))
                ->when($item->color_id, fn($q) => $q->where('purchase_return_items.color_id', $item->color_id))
                ->when($item->size_id, fn($q) => $q->where('purchase_return_items.size_id', $item->size_id))
                ->sum('purchase_return_items.return_qty');

            $receivedQty = (float) $item->quantity_received;

            // Net Billable Qty = (Received Qty - Total Returned Qty)
            $billableQty = max(0, $receivedQty - $totalReturnedQty);

            return [
                'grn_item_id'   => $item->id,
                'po_item_id'    => $item->purchase_order_item_id,
                'item_id'       => $item->item_id,
                'item_name'     => $item->item->name ?? '',
                'item_code'     => $item->item->code ?? '',
                'style_name'    => $item->style->name ?? 'N/A',
                'color_name'    => $item->color->name ?? 'N/A',
                'size_name'     => $item->size->name ?? 'N/A',
                'received_qty'  => $receivedQty,
                'returned_qty'  => $totalReturnedQty,
                'billable_qty'  => $billableQty,
                'unit_price'    => (float) $item->unit_price,
                'subtotal'      => $billableQty * (float) $item->unit_price,
            ];
        })->filter(function ($item) {
            // Skipped whole return
            return $item['billable_qty'] > 0;
        })->values();

        return response()->json([
            'success' => true,
            'grn'     => $grn,
            'items'   => $items
        ]);
    }

    protected function getNextInvoiceNo(){
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $lastInvoice = SupplierInvoice::where('tenant_id', tenant('id'))
            ->where('invoice_no', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        if ($lastInvoice) {
            // INV-2026-001 থেকে শেষের ডিজিট (001) বের করে 1 যোগ করা
            $lastNumber = (int) substr($lastInvoice->invoice_no, strrpos($lastInvoice->invoice_no, '-') + 1);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // বছর শুরু বা প্রথম ইনভয়েস হলে 001 থেকে শুরু হবে
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }

    public function create(){
        $grns = GoodsReceivedNote::with(['supplier', 'purchaseOrder', 'items.item', 'items.color', 'items.size', 'items.style'])
            ->where('tenant_id', tenant('id'))
            ->get();
        $suppliers = Supplier::where('tenant_id', tenant('id'))->where('is_active', 1)->get();

        $nextInvoiceNo = $this->getNextInvoiceNo();

        return view('tenant.purchase.invoice.create', compact('suppliers', 'grns', 'nextInvoiceNo'));
    }

    // ২. Supplier Invoice Save and Ledger Post Engine
    public function store(Request $request)
    {
        $request->validate([
            'grn_id'                      => 'required|exists:goods_received_notes,id',
            'invoice_no'                  => 'required|string|max:255',
            'invoice_date'                => 'required|date',
            'due_date'                    => 'required|date',
            'tax_rate'                    => 'nullable|numeric|min:0',
            'tax_amount'                  => 'nullable|numeric|min:0',
            'discount_amount'             => 'nullable|numeric|min:0',
            'debit_note_adjusted_amount'  => 'nullable|numeric|min:0',
            'items'                       => 'required|array|min:1',
            'items.*.grn_item_id'          => 'required|exists:goods_received_note_items,id',
            'items.*.invoice_qty'          => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request) {
            $grn = GoodsReceivedNote::where('tenant_id', tenant('id'))->findOrFail($request->grn_id);

            // ১. Line Items এর মাধ্যমে প্রকৃত Subtotal হিসাব করা (ইনপুট ম্যানিপুলেশন রোধে)
            $calculatedSubtotal = 0;
            $itemsToStore = [];

            foreach ($request->items as $itemData) {
                $grnItem    = GoodsReceivedNoteItem::findOrFail($itemData['grn_item_id']);
                $invoiceQty = (float) $itemData['invoice_qty'];
                $unitPrice  = (float) $grnItem->unit_price;
                $lineTotal  = $invoiceQty * $unitPrice;

                $itemsToStore[] = [
                    'grnItem'    => $grnItem,
                    'invoiceQty' => $invoiceQty,
                    'unitPrice'  => $unitPrice,
                    'lineTotal'  => $lineTotal,
                ];

                $calculatedSubtotal += $lineTotal;
            }

            // ২. ফাইন্যান্সিয়াল হিসাব ক্যালকুলেশন
            $taxRate         = (float) ($request->tax_rate ?? 0);
            $taxAmount       = (float) ($request->tax_amount ?? 0);
            $discountAmount  = (float) ($request->discount_amount ?? 0);
            $debitNoteAmount = (float) ($request->debit_note_adjusted_amount ?? 0);

            // Net Amount Formula
            $netPayableAmount = max(0, ($calculatedSubtotal + $taxAmount) - $discountAmount - $debitNoteAmount);

            // ৩. Master Supplier Invoice Create
            $invoice = SupplierInvoice::create([
                'tenant_id'                     => tenant('id'),
                'invoice_no'                    => $request->invoice_no,
                'goods_received_note_id'        => $grn->id,
                'purchase_order_id'             => $grn->purchase_order_id,
                'supplier_id'                   => $grn->supplier_id,
                'invoice_date'                  => $request->invoice_date,
                'due_date'                      => $request->due_date,
                'tax_rate'                      => $taxRate,
                'tax_amount'                    => $taxAmount,
                'discount_amount'               => $discountAmount,
                'debit_note_adjusted_amount'    => $debitNoteAmount,
                'sub_total'                     => $calculatedSubtotal,
                'net_amount'                    => $netPayableAmount,
                'status'                        => 'unpaid',
            ]);

            // ৪. Invoice Items Insert
            foreach ($itemsToStore as $data) {
                SupplierInvoiceItem::create([
                    'supplier_invoice_id' => $invoice->id,
                    'grn_item_id'         => $data['grnItem']->id,
                    'item_id'             => $data['grnItem']->item_id,
                    'style_id'            => $data['grnItem']->style_id,
                    'color_id'            => $data['grnItem']->color_id,
                    'size_id'             => $data['grnItem']->size_id,
                    'quantity'            => $data['invoiceQty'],
                    'unit_price'          => $data['unitPrice'],
                    'tax_amount'          => 0,
                    'total_amount'        => $data['lineTotal'],
                ]);
            }

            // ৫. Double Entry Accounting Engine Integration
            $apHead = ChartOfAccount::where('tenant_id', tenant('id'))
                ->where(function ($query) {
                    $query->where('code', 'AP')
                        ->orWhere('code', '2001')
                        ->orWhere('name', 'like', '%Accounts Payable%');
                })->first();

            $clearingHead = ChartOfAccount::where('tenant_id', tenant('id'))
                ->where(function ($query) {
                    $query->where('code', 'GRN-CLEARING')
                        ->orWhere('name', 'like', '%Unbilled Payable%')
                        ->orWhere('name', 'like', '%GRN Clearing%');
                })->first();

            if ($apHead) {
                $voucher = Voucher::create([
                    'tenant_id'    => tenant('id'),
                    'voucher_no'   => 'INV-' . str_replace('/', '-', $invoice->invoice_no),
                    'date'         => $request->invoice_date,
                    'total_amount' => $netPayableAmount,
                    'narration'    => "Supplier Invoice Generated: {$invoice->invoice_no} against GRN: {$grn->grn_no}",
                ]);

                // Ledger Entry 1: CREDIT Accounts Payable (Supplier Bill Liability)
                LedgerEntry::create([
                    'tenant_id'           => tenant('id'),
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $apHead->id,
                    'supplier_id'         => $grn->supplier_id,
                    'debit'               => 0,
                    'credit'              => $netPayableAmount,
                    'narration'           => "Bill Payable for Invoice: {$invoice->invoice_no}",
                ]);

                // Ledger Entry 2: DEBIT GRN Clearing Account (Settling temporary AP Liability)
                if ($clearingHead) {
                    // Balance Match Rule: Net AP Credit == GRN Clearing Debit
                    LedgerEntry::create([
                        'tenant_id'           => tenant('id'),
                        'voucher_id'          => $voucher->id,
                        'chart_of_account_id' => $clearingHead->id,
                        'supplier_id'         => $grn->supplier_id,
                        'debit'               => $netPayableAmount,
                        'credit'              => 0,
                        'narration'           => "GRN Clearing Adjustment for: {$grn->grn_no}",
                    ]);
                }

                $invoice->update(['voucher_id' => $voucher->id]);
            }

            // 🚀 AJAX Response (রিলোড ও ডাটা লস ছাড়া সফল মেসেজ দেওয়া)
            return response()->json([
                'success'      => true,
                'message'      => 'Supplier Invoice created and posted successfully!',
                'redirect_url' => route('tenant.purchase.invoice.index')
            ]);
        });
    }
}
