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
}