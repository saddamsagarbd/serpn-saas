<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ProductionBom;
use App\Models\SalesOrder;
use App\Models\Style;
use Illuminate\Http\Request;

class ProductionBomController extends Controller
{
    public function index(Request $request)
    {
        $boms = ProductionBom::with(['salesOrder.style', 'salesOrder.buyer'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('salesOrder.style', function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
                })->orWhereHas('salesOrder.buyer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('tenant.merchandising.bom.index', compact('boms'));
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
}
