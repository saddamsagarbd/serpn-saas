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
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
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
        ])->get();

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
                    'tenant_id'           => tenant('id'),
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $inventoryHead->id,
                    'debit'               => $totalReceivedValue,
                    'credit'              => 0
                ]);

                // Accounts Payable (Credit)
                LedgerEntry::create([
                    'tenant_id'           => tenant('id'),
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

            $poStatus = $isPartiallyPending ? 'partially_received' : 'received';

            // 'draft','pending','approved','partially_received','received','cancelled'
            // 'received','partially_received','returned'

            $po->update(['status' => $poStatus]);
            $grn->update(['status' => $poStatus]);

            return redirect()->route('tenant.purchase.grn.index')
                ->with('success', 'GRN Verified. Inventory Stock In & Accounting Ledger Successfully Balanced!');
        });
    }

    public function purchaseReturn(Request $request){
        $returns = PurchaseReturn::with([
            'supplier',
            'warehouse',
            'goodsReceivedNote',
        ])->get();

        return view('tenant.purchase.return.index', compact('returns'));
    }

    public function purchaseReturnDetails($tenant, String $id){
        $return = PurchaseReturn::with([
            'supplier',
            'warehouse',
            'goodsReceivedNote',
            'items.itemMaster', 
            'items.stock.itemVariant'
        ])->where('tenant_id', tenant('id'))->findOrFail($id);

        return view('tenant.purchase.return.details', compact('return'));
    }

    public function getGrnItems($tenant, String $id)
    {
        $grn = GoodsReceivedNote::with(['items.itemMaster', 'supplier', 'warehouse'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($id);

        return response()->json([
            'warehouse_id'  => $grn->warehouse_id,
            'supplier_name' => $grn->supplier->name ?? 'N/A',
            'items'         => $grn->items->map(function ($item) {
                return [
                    'item_id' => $item->item_id, // আপনার DB অনুযায়ী item_id-কে পাস করা হচ্ছে
                    'item_name'       => $item->itemMaster->name ?? 'Item #' . $item->item_id,
                    'received_qty'    => $item->quantity_received,
                    'rejected_qty'    => $item->rejected_qty,
                    'unit_price'      => $item->unit_price,
                    'qa_status'       => $item->qa_status,
                    'batch_no'        => $item->batch_no,
                ];
            })
        ]);
    }

    public function purchaseReturnCreate($tenant, Request $request){
        $grns = GoodsReceivedNote::with(['purchaseOrder'])->where('tenant_id', tenant('id'))->get();
        $warehouses = Warehouse::where('tenant_id', tenant('id'))->get();
        return view('tenant.purchase.return.create', compact('grns', 'warehouses'));
    }  

    public function purchaseReturnStore(Request $request){
        $request->validate([
            'grn_id'        => 'required|exists:goods_received_notes,id',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'return_date'   => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.return_qty'      => 'required|numeric|min:0.01',
            'items.*.unit_price'      => 'required|numeric|min:0',
        ]);
        return DB::transaction(function () use ($request) {
            $grn = GoodsReceivedNote::where('tenant_id', tenant('id'))->findOrFail($request->grn_id);

            // ১. Return Header তৈরি
            $returnHeader = PurchaseReturn::create([
                'tenant_id'              => tenant('id'),
                'return_no'              => 'PR-' . date('Ymd') . '-' . rand(1000, 9999),
                'goods_received_note_id' => $grn->id,
                'supplier_id'            => $grn->supplier_id,
                'warehouse_id'           => $request->warehouse_id,
                'return_date'            => $request->return_date,
                'reason'                 => $request->reason ?? 'Damaged/Rejected Material Return',
                'created_by'             => auth()->id(),
                'total_amount'           => 0, // পরে আপডেট হবে
            ]);

            $totalReturnAmount = 0;

            // ২. Item Loop & Stock Reduction
            foreach ($request->items as $itemData) {
                $returnQty = (float) $itemData['return_qty'];
                $unitPrice = (float) $itemData['unit_price'];
                $lineTotal = $returnQty * $unitPrice;

                // Return Line Item Create
                PurchaseReturnItem::create([
                    'purchase_return_id' => $returnHeader->id,
                    'item_id'            => $itemData['item_id'],
                    'return_qty'         => $returnQty,
                    'unit_price'         => $unitPrice,
                    'total_amount'       => $lineTotal,
                    'remarks'            => $itemData['remarks'] ?? null,
                ]);

                // Stock থেকে Qty কমানো
                $stock = Stock::where('tenant_id', tenant('id'))
                    ->where('warehouse_id', $request->warehouse_id)
                    ->where('item_id', $itemData['item_id'])
                    ->first();

                if (!$stock || $stock->available_qty < $returnQty) {
                    throw new \Exception("Insufficient stock in warehouse for Item ID: {$itemData['item_id']}");
                }

                $stock->decrement('available_qty', $returnQty);

                $totalReturnAmount += $lineTotal;
            }

            // Header Amount Update
            $returnHeader->update(['total_amount' => $totalReturnAmount]);

            // ৩. Accounts Double Entry Voucher Generation (Debit Note)
            if ($totalReturnAmount > 0) {
                $inventoryHead = ChartOfAccount::where('tenant_id', tenant('id'))
                    ->where(fn($q) => $q->where('code', '1002')->orWhere('name', 'like', '%Raw Materials%'))
                    ->first();

                $payableHead = ChartOfAccount::where('tenant_id', tenant('id'))
                    ->where(fn($q) => $q->where('code', 'AP')->orWhere('name', 'like', '%Accounts Payable%'))
                    ->first();

                // Accounting Voucher Create
                $voucher = Voucher::create([
                    'tenant_id'    => tenant('id'),
                    'voucher_no'   => 'DN-' . date('Ymd') . '-' . rand(10, 99), // Debit Note Voucher
                    'date'         => $request->return_date,
                    'total_amount' => $totalReturnAmount,
                    'narration'    => "Debit Note issued against GRN Return: " . $returnHeader->return_no,
                ]);

                // Accounts Payable DEBIT (সাপ্লায়ারের দেনা কমবে)
                LedgerEntry::create([
                    'tenant_id'           => tenant('id'),
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $payableHead->id,
                    'debit'               => $totalReturnAmount,
                    'credit'              => 0,
                ]);

                // Inventory Asset CREDIT (স্টকের এসেট ভ্যালু কমবে)
                LedgerEntry::create([
                    'tenant_id'           => tenant('id'),
                    'voucher_id'          => $voucher->id,
                    'chart_of_account_id' => $inventoryHead->id,
                    'debit'               => 0,
                    'credit'              => $totalReturnAmount,
                ]);
            }

            return redirect()->back()->with('success', 'Debit Note created, Stock reduced & Ledger adjusted successfully!');
        });
    }   
}