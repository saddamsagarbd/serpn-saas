<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Item;
use App\Models\LedgerEntry;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\Voucher;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class GrnController extends Controller
{
    public function goodsReceivedNotes(){
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'order.item',
            'order.color',
            'order.size',
            'order.unit',
        ])->where('status', 'Approved')->get();

        $warehouses = Warehouse::where('tenant_id', tenant('id'))->get();
        return view('tenant.purchase.goods-received-notes', compact('purchaseOrders', 'warehouses'));
    }

    public function saveGRNTransaction(Request $request)
    {
        $request->validate([
            'purchase_order_id'     => 'required|exists:purchase_orders,id',
            'warehouse_id'          => 'required|exists:warehouses,id',
            'received_date'         => 'required|date',
            'challan_no'            => 'required|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.po_item_id'    => 'required|exists:purchase_order_items,id',
            'items.*.item_id'       => 'required|exists:item_masters,id',
            'items.*.receiving_qty' => 'nullable|numeric|min:0',
            'items.*.qa_status'     => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $po = PurchaseOrder::where('tenant_id', tenant('id'))->findOrFail($request->purchase_order_id);

            // ১. Header GRN তৈরি
            $grn = GoodsReceivedNote::create([
                'tenant_id'         => tenant('id'),
                'grn_no'            => $request->grn_no ?? ('GRN-' . date('Ymd') . '-' . strtoupper(Str::random(4))),
                'purchase_order_id' => $po->id,
                'supplier_id'       => $po->supplier_id,
                'warehouse_id'      => $request->warehouse_id,
                'received_date'     => $request->received_date,
                'received_by'       => auth()->id(),
                'challan_no'        => $request->challan_no,
                'status'            => 'received',
                'remarks'           => $request->remarks ?? null,
            ]);

            $hasReceivedAnyItem = false;
            $totalReceivedValue = 0; // অ্যাকাউন্টিং ভাউচারের জন্য মোট মূল্য হিসাব

            // ২. GRN Items, Stock Update & PO Item Sync (একমাত্র লুপ)
            foreach ($request->items as $incomingItem) {
                $receivingQty = (float) ($incomingItem['receiving_qty'] ?? 0);

                if ($receivingQty <= 0) {
                    continue;
                }

                $hasReceivedAnyItem = true;
                $poItem = PurchaseOrderItem::find($incomingItem['po_item_id']);
                $unitPrice = $poItem ? $poItem->unit_price : 0;
                $lineTotal = $unitPrice * $receivingQty;

                // GRN Item Entry
                GoodsReceivedNoteItem::create([
                    'goods_received_note_id' => $grn->id,
                    'purchase_order_item_id' => $incomingItem['po_item_id'],
                    'item_id'                => $incomingItem['item_id'],
                    'quantity_received'      => $receivingQty,
                    'rejected_qty'           => ($incomingItem['qa_status'] ?? '') === 'Damaged' ? $receivingQty : 0,
                    'unit_price'             => $unitPrice,
                    'total_amount'           => $lineTotal,
                    'qa_status'              => $incomingItem['qa_status'] ?? 'Good',
                    'batch_no'               => 'BATCH-' . date('Ymd') . '-' . rand(100, 999),
                ]);

                // ভালো মালামালের জন্য ফিজিক্যাল স্টক একবারই বাড়ানো হবে
                if (($incomingItem['qa_status'] ?? 'Good') !== 'Damaged') {
                    Stock::updateOrCreate(
                        [
                            'tenant_id'    => tenant('id'),
                            'warehouse_id' => $request->warehouse_id,
                            'item_id'      => $incomingItem['item_id'],
                        ],
                        [
                            'available_qty' => DB::raw("available_qty + {$receivingQty}"),
                            'created_by'   => auth()->id(),
                            'updated_by'   => auth()->id(),
                        ]
                    );
                    $totalReceivedValue += $lineTotal; 
                }

                // PO Item-এর Received Qty আপডেট
                if ($poItem) {
                    $poItem->increment('received_qty', $receivingQty);
                }
            }

            if (!$hasReceivedAnyItem) {
                throw new \Exception('Please enter valid receiving quantity for at least one item.');
            }

            // ৩. Accounts Double Entry Voucher Posting Engine
            if ($totalReceivedValue > 0) {
                $inventoryHead = ChartOfAccount::where(function ($query) {
                    $query->where('code', '1002')
                        ->orWhere('name', 'like', '%Raw Material%')
                        ->orWhere('name', 'like', '%Inventory%');
                })
                ->where('tenant_id', tenant('id'))
                ->first();

                $payableHead = ChartOfAccount::where(function ($query) {
                    $query->where('code', 'AP')
                        ->orWhere('code', '2001')
                        ->orWhere('name', 'like', '%Accounts Payable%');
                })
                ->where('tenant_id', tenant('id'))
                ->first();

                if (!$inventoryHead || !$payableHead) {
                    throw new \Exception("Accounting Head (Inventory/Payable) not found in Chart of Accounts!");
                }

                $voucher = Voucher::create([
                    'tenant_id'  => tenant('id'),
                    'voucher_no' => 'PV-' . $po->po_no . '-' . rand(10, 99),
                    'date'       => $request->received_date,
                    'total_amount' => $totalReceivedValue,
                    'narration'  => "Material stock received via GRN: " . $grn->grn_no . " against PO: " . $po->po_no,
                ]);

                // Inventory Asset (Debit)
                LedgerEntry::create([
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $inventoryHead->id,
                    'debit'               => $totalReceivedValue,
                    'credit'              => 0
                ]);

                // Accounts Payable (Credit)
                LedgerEntry::create([
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $payableHead->id,
                    'debit'               => 0,
                    'credit'              => $totalReceivedValue
                ]);

                $grn->update(['voucher_id' => $voucher->id]);
            }

            // ৪. PO ও GRN-এর ফাইনাল স্ট্যাটাস আপডেট
            $isPartiallyPending = PurchaseOrderItem::where('purchase_order_id', $po->id)
                ->whereRaw('order_qty > received_qty')
                ->exists();

            $poStatus = $isPartiallyPending ? 'partially_received' : 'completed';

            $po->update(['status' => $poStatus]);
            $grn->update(['status' => $poStatus]);

            return redirect()->route('tenant.purchase.grn.index')
                ->with('success', 'GRN Verified. Inventory Stock In & Accounting Ledger Successfully Balanced!');
        });
    }
}