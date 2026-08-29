<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceController extends Controller
{
    public function index() {
        $invoices = SupplierInvoice::with(['supplier', 'goodsReceivedNote'])
            ->where('tenant_id', tenant('id'))
            ->latest()
            ->paginate(15);

        return view('tenant.purchase.invoice.index', compact('invoices'));
    }

    public function create()
    {
        $suppliers = Supplier::where('tenant_id', tenant('id'))->get();
        $grns = GoodsReceivedNote::where('tenant_id', tenant('id'))
            ->doesntHave('supplierInvoice')
            ->get();

        return view('tenant.purchase.invoice.create', compact('suppliers', 'grns'));
    }

    public function getGrnData(Request $request)
    {
        $grnId = $request->goods_received_note_id;

        $grn = GoodsReceivedNote::with(['items.stock.itemVariant', 'items.itemMaster', 'supplier'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($grnId);

        $debitNoteAmount = PurchaseReturn::where('tenant_id', tenant('id'))
            ->where('goods_received_note_id', $grnId)
            ->with('items')
            ->get()
            ->sum(fn($return) => $return->items->sum('total_amount'));

        return response()->json([
            'status' => 'success',
            'grn' => $grn,
            'debit_note_amount' => $debitNoteAmount
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'goods_received_note_id' => 'required',
            'invoice_no' => 'required|unique:supplier_invoices,invoice_no',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $subTotal = 0;
            foreach ($request->items as $item) {
                $subTotal += ($item['quantity'] * $item['unit_price']);
            }

            $debitNoteAmount = $request->debit_note_adjusted_amount ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            
            $netAmount = max(0, ($subTotal + $taxAmount) - ($discountAmount + $debitNoteAmount));

            $invoice = SupplierInvoice::create([
                'tenant_id' => tenant('id'),
                'supplier_id' => $request->supplier_id,
                'goods_received_note_id' => $request->goods_received_note_id,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'sub_total' => $subTotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'debit_note_adjusted_amount' => $debitNoteAmount,
                'net_amount' => $netAmount,
                'status' => 'unpaid',
                'remarks' => $request->remarks,
            ]);

            foreach ($request->items as $item) {
                SupplierInvoiceItem::create([
                    'supplier_invoice_id' => $invoice->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => ($item['quantity'] * $item['unit_price']),
                ]);
            }

            return redirect()->route('tenant.supplier-invoices.index')
                ->with('success', 'Supplier Invoice created successfully.');
        });
    }

    public function edit($tenant, String $id)
    {
        $invoice = SupplierInvoice::with(['items.itemMaster', 'supplier', 'goodsReceivedNote'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($id);

        return view('tenant.purchase.invoice.edit', compact('invoice'));
    }

    public function update(Request $request, $tenant, String $id)
    {
        $invoice = SupplierInvoice::where('tenant_id', tenant('id'))->findOrFail($id);

        $request->validate([
            'invoice_no' => 'required|unique:supplier_invoices,invoice_no,' . $invoice->id,
            'invoice_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $subTotal = 0;
            foreach ($request->items as $item) {
                $subTotal += ($item['quantity'] * $item['unit_price']);
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $debitNoteAmount = $invoice->debit_note_adjusted_amount;

            $netAmount = max(0, ($subTotal + $taxAmount) - ($discountAmount + $debitNoteAmount));

            $invoice->update([
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'sub_total' => $subTotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'net_amount' => $netAmount,
                'remarks' => $request->remarks,
            ]);

            // পুরাতন আইটেম মুছে নতুন আইটেম আপডেট
            $invoice->items()->delete();
            foreach ($request->items as $item) {
                SupplierInvoiceItem::create([
                    'supplier_invoice_id' => $invoice->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => ($item['quantity'] * $item['unit_price']),
                ]);
            }
        });

        return redirect()->route('tenant.supplier-invoices.index')
            ->with('success', 'Supplier Invoice updated successfully.');
    }
}
