<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ProductionBom;
use App\Models\SalesOrder;
use App\Models\Style;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductionBomController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = ProductionBom::with([
                'salesOrder.style', 
                'salesOrder.buyer', 
                'salesOrder.items', 
                'items'
            ])
            ->where('tenant_id', tenant('id'))
            ->when($request->search, function ($q, $search) {
                $q->whereHas('salesOrder.style', function ($styleQ) use ($search) {
                    $styleQ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })->orWhereHas('salesOrder.buyer', function ($buyerQ) use ($search) {
                    $buyerQ->where('name', 'like', "%{$search}%");
                });
            })
            ->latest();

            // dd($query->get());
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('style_code', function ($row) {
                    return $row->salesOrder->style->style_number ?? 'N/A';
                })
                ->addColumn('style_name', function ($row) {
                    return $row->salesOrder->style->product_name ?? $row->product_name ?? 'N/A';
                })
                ->addColumn('buyer_name', function ($row) {
                    return $row->salesOrder->buyer->name ?? ($row->buyer ? $row->buyer->name : 'N/A');
                })
                ->addColumn('mpr_po_no', function ($row) {
                    return $row->salesOrder->buyer_po_number ?? $row->mpr_po_no ?? 'N/A';
                })
                ->addColumn('sales_order_total', function ($row) {
                    return $row->salesOrder ? (float) $row->salesOrder->total_quantity : 0;
                })
                ->addColumn('total_budget', function ($row) {
                    return (float) ($row->grand_total ?? $row->total_budget ?? 0);
                })
                ->addColumn('total_paid', function ($row) {
                    return (float) ($row->total_paid ?? 0);
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d') : 'N/A';
                })
                ->make(true);
        }

        return view('tenant.merchandising.bom.index');
    }
    
    public function create(Request $request)
    {
        $styles = Style::select('id', 'style_number as style_code', 'product_name as style_name')->get();
        $selectedStyleId = $request->query('style_id');
        $selectedMprId = $request->query('mpr_id');

        $selectedMpr = null;
        if ($selectedMprId) {
            $selectedMpr = SalesOrder::with(['items.colorContext', 'items.sizeChart', 'buyer'])->find($selectedMprId);
        }

        return view('tenant.merchandising.bom.create', compact('styles', 'selectedStyleId', 'selectedMpr', 'selectedMprId'));
    }

    public function getMprsByStyle($tenant, String $styleId)
    {
        $mprs = SalesOrder::where('style_id', $styleId)
            ->select('id', 'order_number', 'buyer_po_number', 'created_at')
            ->get();

        return response()->json($mprs);
    }

    public function store(Request $request, $tenant, $id = null) 
    {
        $validated = $request->validate([
            'style_id'                    => 'required|exists:styles,id',
            'sales_order_id'              => 'required|exists:sales_orders,id', // ba sales_orders,id
            'grand_total'                 => 'required|numeric|min:0',
            'remarks'                     => 'nullable|string',
            'items'                       => 'required|array|min:1',
            'items.*.cost_type'           => 'required|string|in:Material,Processing',
            'items.*.cost_head'           => 'nullable|string',
            'items.*.cat_id'              => 'nullable',
            'items.*.cat_name'            => 'nullable|string',
            'items.*.item_id'             => 'nullable',
            'items.*.item_name'           => 'required|string',
            'items.*.matrix_target'       => 'nullable|string',
            'items.*.sales_order_item_id' => 'nullable',
            'items.*.color_name'          => 'nullable|string',
            'items.*.garment_qty'         => 'required|numeric|min:0',
            'items.*.consumption'         => 'required|numeric|gt:0',
            'items.*.req_qty'             => 'required|numeric|gt:0',
            'items.*.unit_price'          => 'required|numeric|min:0',
            'items.*.total_cost'          => 'required|numeric|min:0',
        ]);

        $currentUserId = auth()->id();

        DB::beginTransaction();

        try {
            // 1. Identify record if update
            $bom = ProductionBom::find($id);

            if ($bom) {
                // Update existing BOM
                $bom->update([
                    'style_id'       => $validated['style_id'],
                    'sales_order_id' => $validated['sales_order_id'],
                    'grand_total'    => $validated['grand_total'],
                    'remarks'        => $validated['remarks'] ?? null,
                    'updated_by'     => $currentUserId,
                ]);
            } else {
                // Create new BOM
                $bom = ProductionBom::create([
                    'style_id'       => $validated['style_id'],
                    'sales_order_id' => $validated['sales_order_id'],
                    'grand_total'    => $validated['grand_total'],
                    'tenant_id'      => tenant('id') ?? null,
                    'bom_number'     => 'BOM-' . date('Y') . '-' . str_pad((ProductionBom::max('id') + 1), 5, '0', STR_PAD_LEFT),
                    'remarks'        => $validated['remarks'] ?? null,
                    'created_by'     => $currentUserId,
                ]);
            }

            // 2. Purono items delete kora
            $bom->items()->delete();

            // 3. Batch insert items with schema-matched array keys
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemsData[] = [
                    'cost_type'           => $item['cost_type'] ?? 'Material',
                    'cost_head'           => $item['cost_head'] ?? null,
                    'category_id'         => $item['cat_id'] ?? null,
                    'category_name'       => $item['cat_name'] ?? null,
                    
                    // Fixed schema column: 'item_id'
                    'item_id'         => is_numeric($item['item_id'] ?? null) ? $item['item_id'] : null,
                    'item_name'           => $item['item_name'],
                    
                    'matrix_target'       => $item['matrix_target'] ?? 'ALL',
                    'sales_order_item_id' => $item['sales_order_item_id'] ?? null,
                    'color_name'          => $item['color_name'] ?? null,
                    'garment_qty'         => $item['garment_qty'],
                    'consumption'         => $item['consumption'],
                    'req_qty'             => $item['req_qty'],
                    'unit_price'          => $item['unit_price'],
                    'total_cost'          => $item['total_cost'],
                ];
            }

            // Single query bulk insert
            $bom->items()->createMany($itemsData);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Production BOM saved successfully!',
                'redirect_url' => route('tenant.merch.bom.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving BOM: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf($tenant, String $id){

        $bom = ProductionBom::with([
            'salesOrder.style', 
            'salesOrder.buyer', 
            'salesOrder.items', 
            'items'
        ])->findOrFail($id);
        
        // Render the view to raw HTML then feed it to DomPDF engine
        $pdf = Pdf::loadView('tenant.exports.production-bom', compact('bom'))
              ->setPaper('a4', 'portrait');

        $styleCode = $bom->salesOrder->style->code 
                 ?? $bom->salesOrder->style->style_number 
                 ?? $bom->style_number 
                 ?? 'BOM-' . $bom->id;

        $pdfFileName = 'BOM_' . str_replace([' ', '/', '\\'], '_', $styleCode) . '.pdf';

        return $pdf->stream($pdfFileName);
    }
}