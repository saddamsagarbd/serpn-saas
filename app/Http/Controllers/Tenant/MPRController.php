<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ColorContext;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SizeChart;
use App\Models\Style;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class MPRController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()){
            $data = SalesOrder::with(['buyer', 'items'])->where('tenant_id', tenant('id'))->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('buyer_po', function($row){
                    return $row->buyer_po_number ? $row->buyer_po_number : 'N/A';
                })
                ->addColumn('buyer_name', function($row){
                    if (!$row->buyer) {
                        return 'N/A';
                    }
                    
                    return html_entity_decode(
                        html_entity_decode($row->buyer->name, ENT_QUOTES, 'UTF-8'), 
                        ENT_QUOTES, 
                        'UTF-8'
                    );
                })
                ->editColumn('po_date', function($row){
                    return $row->po_received_date ?? $row->created_at;
                })
                ->editColumn('delivery_date', function($row){
                    return $row->requested_delivery_date ?? 'N/A';
                })
                ->editColumn('job_mode', function($row){
                    return $row->job_mode ? ($row->distribution_channel ?? '' )."/".$row->job_mode: 'N/A';
                })
                ->editColumn('currency', function($row){
                    return $row->currency ?? 'USD';
                })
                ->editColumn('total_amount', function($row){
                    return $row->total_amount ?? 0.00;
                })
                ->editColumn('status', function($row){
                    return $row->status ?? 'Draft';
                })
                ->rawColumns(['action', 'status', 'currency', 'buyer_name'])
                ->make(true);
            
        }
        return view('tenant.merchandising.mpr.index');
    }
    public function createMrpOrder() {        
        $styles = Style::with(['buyer', 'season', 'costing'])->where('tenant_id', tenant('id'))->get();
        $colors = ColorContext::get();
        $sizes = SizeChart::get();
        return view('tenant.merchandising.mpr.order-form', compact('styles', 'colors', 'sizes'));
    }
    // Example Format: ORD-202608-0001 (Prefix-YearMonth-Sequential Number)
    public function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Ym') . '-';
        
        $lastOrder = SalesOrder::where('order_number', 'like', $prefix . '%')
                        ->latest('id')
                        ->first();

        if (!$lastOrder) {
            $number = 1;
        } else {
            // শেষ অর্ডারের নাম্বারের সাথে ১ যোগ করা
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $number = $lastNumber + 1;
        }

        // ৪ ডিজিটের প্যাডিং সহ কোড রিটার্ন (যেমন: 0001, 0002)
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function mrpOrderCreate(Request $request) {

        // dd($request->all());
        
        $request->validate([
            'sales_org'               => 'nullable|string',
            'distribution_channel'    => 'required|string', // Export / Domestic
            'job_mode'                => 'required|string', // FOB / CMPTW / CM
            'division'                => 'required|string', // Merchant Team
            'buyer_id'                => 'required|exists:buyers,id',
            'ship_to_party'           => 'required|string',
            'buyer_po_number'         => 'required|string',
            'po_received_date'        => 'required|date',
            'advance_receive_date'    => 'nullable|date',
            'requested_delivery_date' => 'required|date|after_or_equal:po_received_date',
            'currency'                => 'nullable|string',

            // Item Matrix Validation
            'items'                   => 'required|array|min:1',
            'items.*.style_id'        => 'required|exists:styles,id',
            'items.*.sku'             => 'required|string',
            'items.*.color'           => 'required|string',
            'items.*.size'            => 'required|string',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.plant'           => 'required|string',
            'items.*.shipping_point'  => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // ২. Parent Sales Order তৈরি
            $salesOrder = SalesOrder::create([
                'tenant_id'               => tenant('id'),
                'sales_org'               => $request->sales_org ?? 'House 57 Bangladesh',
                'distribution_channel'    => $request->distribution_channel,
                'job_mode'                => $request->job_mode,
                'division'                => $request->division,
                'buyer_id'                => $request->buyer_id,
                'ship_to_party'           => $request->ship_to_party,
                'buyer_po_number'         => $request->buyer_po_number,
                'po_received_date'        => $request->po_received_date,
                'advance_receive_date'    => $request->advance_receive_date,
                'requested_delivery_date' => $request->requested_delivery_date,
                'currency'                => $request->currency ?? 'USD',
                'status'                  => 'Draft',
                'created_by'              => auth()->id(),
            ]);

            // ৩. Child Items প্রসেস করা
            $orderItemsData = [];
            $now = now();

            foreach ($request->items as $item) {
                $orderItemsData[] = [
                    'sales_order_id' => $salesOrder->id,
                    'style_id'       => $item['style_id'],
                    'sku'            => $item['sku'],
                    'color'          => $item['color'],
                    'size'           => $item['size'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'plant'          => $item['plant'],
                    'shipping_point' => $item['shipping_point'],
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            if(empty($orderItemsData)){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No Item fount'
                ], 500);
                
            }

            // ৪. Child Table-এ Bulk Insert (পারফরম্যান্স ফাস্ট করার জন্য)
            SalesOrderItem::insert($orderItemsData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Order successfully generated.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Sales Order: ' . $e->getMessage()
            ], 500);
        }
        
    }

    public function exportPdf(String $tenant, String $id)
    {
        $salesOrder = SalesOrder::with([
            'buyer',                     // Line item-এর Size relation
            'items.style.bomItems.itemMaster', // Style -> bomItems
            'items.style.bomItems.color',
            'items.style.bomItems.size',
        ])->where('tenant_id', tenant('id'))->findOrFail($id);

        $consolidatedMrpDetails = $this->calculateMrpDetails($salesOrder);

        // Render the view to raw HTML then feed it to DomPDF engine
        $pdf = Pdf::loadView('tenant.exports.mpr_pdf', compact('salesOrder', 'consolidatedMrpDetails'))
        ->setPaper('a4', 'portrait')
        ->setOption([
            'isRemoteEnabled'      => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont'          => 'sans-serif'
        ]);

        $poNumber    = $salesOrder->buyer_po_number ? Str::slug($salesOrder->buyer_po_number) : $id;
        $pdfFileName = 'MPR_SO_' . $poNumber . '.pdf';
        
        // Use stream() to preview in-browser, download() to force save file
        return $pdf->stream($pdfFileName);
    }

    private function calculateMrpDetails($salesOrder): array
    {
        $bomConsolidated = [];
        $totalOrderQty   = $salesOrder->items->sum('quantity') ?? $salesOrder->items->sum('qty') ?? 0;

        foreach ($salesOrder->items as $soItem) {
            $orderQty = floatval($soItem->quantity ?? $soItem->qty ?? 0);
            $costing  = $soItem->style?->costing;

            if ($costing && $costing->bomItems) {
                foreach ($costing->bomItems as $bom) {
                    // Unique Grouping Key (Item + Color + Size)
                    $key = $bom->item_id . '_' . $bom->color_id . '_' . $bom->size_id;

                    $consumption = floatval($bom->consumption ?? 0);
                    $requiredQty = $orderQty * $consumption;

                    if (!isset($bomConsolidated[$key])) {
                        $bomConsolidated[$key] = [
                            'item_name'    => $bom->item_description ?? $bom->item?->name ?? 'N/A',
                            'color_name'   => $bom->color?->name ?? 'N/A',
                            'size_name'    => $bom->size?->short_name ?? $bom->size?->name ?? 'N/A',
                            'consumption'  => $consumption,
                            'unit'         => $bom->item_unit ?? $bom->item?->uom ?? 'Pcs',
                            'required_qty' => 0,
                        ];
                    }

                    $bomConsolidated[$key]['required_qty'] += $requiredQty;
                }
            }
        }

        return [
            'total_order_qty' => $totalOrderQty,
            'bom_items'       => array_values($bomConsolidated),
        ];
    }

    public function mrpOrderEdit($tenant, String $id){
        $salesOrder = SalesOrder::with(['items', 'buyer'])->where('tenant_id', tenant('id'))->findOrFail($id);
        $styles = Style::with(['buyer', 'costing'])->get();
        $colors = ColorContext::all();
        $sizes = SizeChart::all();
        
        return view('tenant.merchandising.mpr.order-form', compact('styles', 'colors', 'sizes', 'salesOrder'));
    }

    public function mrpOrderDetails(Request $request, $tenant, String $id){

        $salesOrder = SalesOrder::with([
            'buyer',                     // Line item-এর Size relation
            'items.style.bomItems.itemMaster', // Style -> bomItems
            'items.style.bomItems.color',
            'items.style.bomItems.size',
        ])->where('tenant_id', tenant('id'))->findOrFail($id);

        $totalOrderQty = $salesOrder->items->sum('quantity');

        $aggregatedBomItems = collect();

        foreach ($salesOrder->items as $line) {
            $orderQty = $line->quantity ?? 0;

            if ($line->style && $line->style->bomItems) {
                foreach ($line->style->bomItems as $bom) {
                    // ইউনিক কি তৈরি করা হচ্ছে (যেন একই আইটেম, কালার ও সাইজ বার বার না এসে যোগ হয়)
                    $itemId   = $bom->item_master_id ?? $bom->id;
                    $colorId  = $bom->color_id ?? 'default';
                    $sizeId   = $bom->size_id ?? 'default';
                    $uniqueKey = "{$itemId}_{$colorId}_{$sizeId}";

                    $requiredQtyForThisLine = ($bom->consumption ?? 0) * $orderQty;

                    if ($aggregatedBomItems->has($uniqueKey)) {
                        // একই আইটেম আগে পাওয়া গেলে Required Qty যোগ হবে
                        $existing = $aggregatedBomItems->get($uniqueKey);
                        $existing['required_qty'] += $requiredQtyForThisLine;
                        $aggregatedBomItems->put($uniqueKey, $existing);
                    } else {
                        // নতুন আইটেম যুক্ত করা
                        $aggregatedBomItems->put($uniqueKey, [
                            'item_name'    => $bom->itemMaster->name ?? $bom->item_description ?? 'N/A',
                            'color_name'   => $bom->color->name ?? 'N/A',
                            'size_name'    => $bom->size->name ?? 'N/A',
                            'consumption'  => $bom->consumption ?? 0,
                            'unit'         => $bom->itemMaster->unit->short_name ?? 'N/A',
                            'unit_price'   => $bom->unit_price ?? 0,
                            'required_qty' => $requiredQtyForThisLine,
                        ]);
                    }
                }
            }
        }

        $consolidatedMrpDetails = [
            'total_order_qty' => $totalOrderQty,
            'bom_items'       => $aggregatedBomItems->values() // রি-ইনডেক্সিং
        ];

        return view('tenant.merchandising.mpr.report', compact('salesOrder', 'consolidatedMrpDetails'));
    }

    public function update(Request $request, $tenant, String $id)
    {
        $request->validate([
            'sales_org'               => 'nullable|string',
            'distribution_channel'    => 'required|string', // Export / Domestic
            'job_mode'                => 'required|string', // FOB / CMPTW / CM
            'division'                => 'required|string', // Merchant Team
            'buyer_id'                => 'required|exists:buyers,id',
            'ship_to_party'           => 'required|string',
            'buyer_po_number'         => 'required|string',
            'po_received_date'        => 'required|date',
            'advance_receive_date'    => 'nullable|date',
            'requested_delivery_date' => 'required|date|after_or_equal:po_received_date',
            'currency'                => 'nullable|string',

            // Item Matrix Validation
            'items'                   => 'required|array|min:1',
            'items.*.style_id'        => 'required|exists:styles,id',
            'items.*.sku'             => 'required|string',
            'items.*.color'           => 'required|string',
            'items.*.size'            => 'required|string',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.plant'           => 'required|string',
            'items.*.shipping_point'  => 'required|string',
        ]);

        try {
            $salesOrder = SalesOrder::findOrFail($id);

            // Header Update
            $salesOrder->update([
                'sales_org'             => $request->sales_org,
                'distribution_channel'  => $request->distribution_channel,
                'job_mode'              => $request->job_mode,
                'division'              => $request->division,
                'buyer_id'              => $request->buyer_id,
                'ship_to_party'         => $request->ship_to_party,
                'buyer_po_number'       => $request->buyer_po_number,
                'po_received_date'      => $request->po_received_date,
                'advance_receive_date'  => $request->advance_receive_date,
                'requested_delivery_date' => $request->requested_delivery_date,
                'currency'              => $request->currency,
            ]);

            // Items Re-syncing (পুরনো আইটেম ডিলিট করে নতুন আইটেম সেট করা)
            $salesOrder->items()->delete();

            foreach ($request->items as $item) {
                $salesOrder->items()->create([
                    'style_id'       => $item['style_id'],
                    'sku'            => $item['sku'],
                    'color'          => $item['color'],
                    'size'           => $item['size'],
                    'plant'          => $item['plant'],
                    'shipping_point' => $item['shipping_point'],
                    'unit_price'     => $item['unit_price'],
                    'quantity'       => $item['quantity'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}