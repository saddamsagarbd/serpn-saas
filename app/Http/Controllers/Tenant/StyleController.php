<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\ColorContext;
use App\Models\Season;
use App\Models\SizeChart;
use App\Models\Style;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StyleController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Style::with(['buyer', 'season', 'costing'])->where('tenant_id', tenant('id'))->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('buyer_name', function($row){
                    return $row->buyer ? $row->buyer->name : 'N/A';
                })
                ->addColumn('season_name', function($row){
                    return $row->season ? $row->season->name : 'N/A';
                })
                ->editColumn('style_name', function($row){
                    return $row->product_name ?: 'N/A';
                })
                ->editColumn('style_code', function($row){
                    return $row->style_code ?: 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tenant.merchandising.styles.index');
    }
    public function createStyle(){
        $colors = ColorContext::all();
        $units = Unit::all();
        $buyers = Buyer::all();
        $seasons = Season::all();
        $sizes = SizeChart::all();
        return view('tenant.merchandising.styles.create', compact('colors', 'units', 'buyers', 'seasons','sizes'));
    }

    public function styleStore(Request $request){
        $request->validate([
            'style_code'   => [
                'required',
                'string',
                Rule::unique('styles', 'style_number')
                    ->where('tenant_id', tenant('id'))
            ],
            'style_name'   => 'required|string|max:255',
            'buyer_id'     => 'required|exists:buyers,id',
            'season_id'    => 'required|exists:seasons,id',
            'currency'     => 'nullable|string|max:10',
            'target_price' => 'nullable|numeric|min:0',
            'offered_price'=> 'nullable|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'service_cost'    => 'nullable|numeric|min:0',
            'revenue_percent' => 'nullable|numeric|min:0|max:100',
            'ait_percent'     => 'nullable|numeric|min:0|max:100',
            'vat_percent'     => 'nullable|numeric|min:0|max:100',
            
            // Ensure the Alpine array payload is present and structured safely
            'items'        => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_type' => 'required',
            'items.*.color_id'  => 'required|exists:color_contexts,id',
            'items.*.size_id'   => 'required|exists:size_charts,id',
            'items.*.qty'   => 'required|numeric',
            'items.*.cost'   => 'required',
        ]);
        
        DB::beginTransaction();

        try {

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('styles', 'public');
            }

            $style = Style::create([
                'tenant_id'    => tenant('id'),
                'buyer_id'     => $request->buyer_id,
                'season_id'    => $request->season_id,
                'style_number' => $request->style_code,
                'product_name' => $request->style_name,
                'image'        => $imagePath,
                'created_by'   => auth()->id()
            ]);

            // 2. Save the initial BOM costing sheet instance
            $costing = $style->costing()->create([
                'tenant_id'          => tenant('id'),
                'currency'           => $request->currency ?? 'USD',
                'target_fob'         => $request->target_price ?? 0.0000,
                'offered_fob'        => $request->offered_price ?? $request->target_price ?? 0.0000,
                'total_service_cost' => $request->service_cost ?? 0.0000,
                'revenue_percent'    => $request->revenue_percent ?? 0.00,
                'ait_percent'        => $request->ait_percent ?? 0.00,
                'vat_percent'        => $request->vat_percent ?? 0.00,
                'status'             => 'draft',
            ]);            
        
            // 3. Loop through your dynamic Alpine items array
            foreach ($request->items as $item) {
                $qty = (float)($item['qty'] ?? 0);
                $cost = (float)($item['cost'] ?? 0);
                $itemId = $item['item_id'] ?? null;

                $costing->bomItems()->create([
                    'tenant_id'        => tenant('id'),
                    'category'         => $item['item_type'],
                    'item_id'          => $itemId,
                    'item_description' => $item['item_name'],
                    'consumption'      => $qty,
                    'color_id'         => $item['color_id'],
                    'size_id'          => $item['size_id'],
                    'item_unit'        => $itemId ? getItemUnit($itemId) : 'Pcs',
                    'unit_price'       => $cost,
                    'total_cost'       => $qty * $cost
                ]);
            }

            // 4. Fire the new model utility method to tally the total material costs
            if (method_exists($costing, 'recalculateCosting')) {
                $costing->recalculateCosting();
            } else {
                $costing->updateCalculatedTotalRmCost();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Style and initial BOM created successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Style Store Failed: ' . $e->getMessage(), [
                'tenant_id' => tenant('id'),
                'user_id'   => auth()->id(),
                'trace'     => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while saving style: ' . $e->getMessage()
            ], 500);
        }

    }

    public function edit($tenant, String $id)
    {
        $style = Style::with(['costing.bomItems'])->where('tenant_id', tenant('id'))->findOrFail($id);
        $colors = ColorContext::all();
        $units = Unit::all();
        $buyers = Buyer::all();
        $seasons = Season::all();
        $sizes = SizeChart::all();

        return view('tenant.merchandising.styles.create', compact('style', 'colors', 'units', 'buyers', 'seasons','sizes'));
    }

    // --- 2. Process Style & BOM Update ---
    public function update(Request $request, $tenant, String $id)
    {
        $style = Style::where('tenant_id', tenant('id'))->findOrFail($id);

        $request->validate([
            'style_code'   => 'required|string|unique:styles,style_number,'.$style->id.',id,tenant_id,'.tenant('id'),
            'style_name'   => 'required|string|max:255',
            'buyer_id'     => 'required|exists:buyers,id',
            'season_id'    => 'required|exists:seasons,id',
            'target_price' => 'nullable|numeric|min:0',
            'items'        => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update Style Header
            $style->update([
                'buyer_id'     => $request->buyer_id,
                'season_id'    => $request->season_id,
                'style_number' => trim($request->style_code),
                'product_name' => trim($request->style_name),
                'updated_by' => auth()->id()
            ]);

            // Update Target Costing Info
            $costing = $style->costing;
            $costing->update([
                'target_fob' => $request->target_price ?? 0.00,
            ]);

            // Wipe old BOM items and recreate (cleanest way to update dynamic grids)
            $costing->bomItems()->delete();
            foreach ($request->items as $item) {
                $costing->bomItems()->create([
                    'tenant_id'   => tenant('id'),
                    'category' => $item['item_type'],
                    'item_description' => $item['item_name'],
                    'consumption' => $item['qty'] ?? 0,
                    'color_id' => $item['color_id'],
                    'size_id' => $item['size_id'],
                    'item_unit' => $item['item_type'] === 'fabric' ? 'Kg' : 'Pcs',
                    'unit_price' => $item['cost'] ?? 0,
                    'total_cost' => ($item['qty'] ?? 0) * ($item['cost'] ?? 0)
                ]);
            }

            // Tally totals up again
            $costing->updateCalculatedTotalRmCost();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Style updated flawlessly!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($tenant, String $id)
    {
        $style = Style::with(['buyer', 'season', 'costing.bomItems'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($id);

        return view('tenant.merchandising.styles.show', compact('style'));
    }

    public function exportPdf(String $tenant, String $id)
    {
        $style = Style::with(['buyer', 'season', 'costing.bomItems.color', 'costing.bomItems.size'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($id);

        // Render the view to raw HTML then feed it to DomPDF engine
        $pdf = Pdf::loadView('tenant.exports.style_costing_pdf', compact('style'))
                ->setPaper('a4', 'portrait');

        $pdfFileName = 'BOM_' . str_replace(' ', '_', $style->style_number) . '.pdf';
        
        // Use stream() to preview in-browser, download() to force save file
        return $pdf->stream($pdfFileName);
    }

    public function delete($tenant, String $id)
    {
        $unit = Style::findOrFail($id);
        $unit->update([
            'status' => 'inactive'
        ]);

        return redirect()->back()->with('success', 'Style deleted successfully!');
    }
}