<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Style;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    public const SUPPLIER_TYPES = [
        "fabrics"  => 'Fabrics',
        "trims" => 'Trims & Accessories',
        "yarn" => 'Yarn',
        "packaging" => 'Packaging',
        "general" => 'General / Service'
    ];
    
    public function index(Request $request){
        if($request->ajax()){
            $orders = PurchaseOrder::with(['supplier', 'order.item'])->where('tenant_id', tenant('id'))->get();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('po_no', function($row){
                    return $row->po_no ?? 'N/A';
                })
                ->addColumn('supplier_details', function($row){
                    if (!$row->supplier) {
                        return 'N/A';
                    }
                    
                    return html_entity_decode(
                        html_entity_decode($row->supplier->name." (". $row->supplier->phone .")", ENT_QUOTES, 'UTF-8'), 
                        ENT_QUOTES, 
                        'UTF-8'
                    );
                })
                ->editColumn('order_details', function($row){
                    if ($row->order->isEmpty()) {
                        return 'N/A';
                    }
                    return $row->order->map(function ($orderItem) {
                        $itemName = $orderItem->item->name ?? 'Item';
                        $qty = $orderItem->mpr_qty ?? 0;

                        return "{$itemName}: {$qty}";
                    })->implode(', ');
                })
                ->rawColumns(['action', 'po_no', 'supplier_details', 'order_details'])
                ->make(true);
        }

        return view('tenant.purchase.order.index');        
    }

    public function create(){
        $suppliers = Supplier::where('tenant_id', tenant('id'))->where('is_active', 1)->get();
        $styles = Style::where('tenant_id', tenant('id'))->get();
        $supplier_types = self::SUPPLIER_TYPES;
        return view('tenant.purchase.order.create', compact('suppliers', 'supplier_types', 'styles'));        
    }

    public function store($tenant, Request $request){
        $tenantId = tenant('id');
        
        $data = $request->validate([
            'supplier_id'         => 'required|exists:suppliers,id',
            'style_id'            => 'required|exists:styles,id',
            'po_date'             => 'required|date',
            'delivery_date'       => 'nullable|date|after_or_equal:po_date',
            'payment_terms_text'  => 'nullable|string|max:255',
            'remarks'             => 'nullable|string',
            'status'              => 'required|in:draft,pending,approved',
            
            // বিলিং ফিল্ডস
            'subtotal'            => 'nullable|numeric|min:0',
            'transport_cost'      => 'nullable|numeric|min:0',
            'loader_bill'          => 'nullable|numeric|min:0',
            'inspection_bill'      => 'nullable|numeric|min:0',
            'extra_charges'       => 'nullable|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0',
            'grand_total'         => 'required|numeric|min:0',

            // আইটেমস Array
            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'nullable|exists:item_masters,id',
            'items.*.color_id'    => 'nullable',
            'items.*.size_id'     => 'nullable',
            'items.*.unit_id'     => 'nullable',
            'items.*.mpr_qty'     => 'nullable|numeric',
            'items.*.order_qty'   => 'required|numeric|gt:0',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $poNo = 'PO-' . date('Y') . '-' . str_pad(PurchaseOrder::max('id') + 1, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'tenant_id'          => tenant('id'),
                'po_no'              => $poNo,
                'supplier_id'        => $data['supplier_id'],
                'style_id'           => $data['style_id'],
                'po_date'            => $data['po_date'],
                'delivery_date'      => $data['delivery_date'] ?? null,
                'subtotal'           => $data['subtotal'] ?? 0,
                'transport_cost'     => $data['transport_cost'] ?? 0,
                'loader_bill'         => $data['loader_bill'] ?? 0,
                'inspection_bill'     => $data['inspection_bill'] ?? 0,
                'extra_charges'      => $data['extra_charges'] ?? 0,
                'discount'           => $data['discount'] ?? 0,
                'grand_total'        => $data['grand_total'],
                'due_amount'         => $data['grand_total'], // শুরুতে পুরো টাকাই Due
                'payment_terms_text' => $data['payment_terms_text'] ?? null,
                'status'             => $data['status'],
                'remarks'            => $data['remarks'] ?? null,
            ]);

            $poItems = [];

            foreach ($data['items'] as $item) {
                $totalPrice = floatval($item['order_qty']) * floatval($item['unit_price']);

                $poItems[]=[
                    'tenant_id'          => tenant('id'),
                    'purchase_order_id' => $po->id,
                    'item_id'           => $item['item_id'] ?? null,
                    'color_id'          => $item['color_id'] ?? null,
                    'size_id'           => $item['size_id'] ?? null,
                    'unit_id'           => $item['unit_id'] ?? null,
                    'mpr_qty'           => $item['mpr_qty'] ?? 0,
                    'order_qty'         => $item['order_qty'],
                    'unit_price'        => $item['unit_price'],
                    'total_price'       => $totalPrice,
                ];

            }
            
            if(empty($poItems)){
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store Purchase Order.'
                ], 500);
            }
            PurchaseOrderItem::insert($poItems);
            DB::commit();

            // ৫. ইমেইল বা নোটিফিকেশন পাঠানোর অপশন (Approved হলে)
            if ($po->status === 'approved') {
                // Mail::to($po->supplier->email)->queue(new PurchaseOrderMail($po));
            }
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order successfully created with PO No: ' . $poNo,
                'po_id'   => $po->id
            ], 201);
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to store Purchase Order. ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function edit($tenant, String $id){
        $suppliers = Supplier::where('tenant_id', tenant('id'))->where('is_active', 1)->get();
        $styles = Style::where('tenant_id', tenant('id'))->get();
        $supplier_types = self::SUPPLIER_TYPES;
        $orders = PurchaseOrder::with(['supplier', 'order.item'])->where('tenant_id', tenant('id'))->where('id', $id)->first();
        return view('tenant.purchase.order.create', compact('suppliers', 'supplier_types', 'styles', 'orders'));        
    }
}