<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\ColorContext;
use App\Models\Season;
use App\Models\SizeChart;
use App\Models\Style;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StyleController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Style::with(['buyer', 'season', 'costing'])->latest();

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
        return view('tenant.inventory.style.index');
    }
    public function createStyle(){
        $colors = ColorContext::all();
        $units = Unit::all();
        $buyers = Buyer::all();
        $seasons = Season::all();
        $sizes = SizeChart::all();
        return view('tenant.inventory.style.create', compact('colors', 'units', 'buyers', 'seasons','sizes'));
    }

    public function styleStore(Request $request){
        $request->validate([
            'style_code'   => [
                'required',
                'string',
                // Corrects global uniqueness issue by isolating rule evaluation to the current tenant
                Rule::unique('styles', 'style_number')
                    ->where('tenant_id', tenant('id'))
            ],
            'style_name'   => 'required|string|max:255',
            'buyer_id'     => 'required|exists:buyers,id',
            'season_id'    => 'required|exists:seasons,id',
            'target_price' => 'nullable|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Ensure the Alpine array payload is present and structured safely
            'items'        => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_type' => 'required|in:fabric,trim',
            'items.*.color_id'  => 'nullable|exists:color_contexts,id',
            'items.*.size_id'   => 'nullable|exists:size_charts,id',
        ]);
        DB::beginTransaction();

        try {

            $style = Style::create([
                'tenant_id' => tenant('id'),
                'buyer_id' => $request->buyer_id,
                'season_id' => $request->season_id,
                'style_number' => $request->style_code,
                'product_name' => $request->style_name,
                'created_by' => auth()->id()
            ]);

            // 2. Save the initial BOM costing sheet instance
            $costing = $style->costing()->create([
                'tenant_id'   => tenant('id'),
                'target_fob' => $request->target_price ?? 0.00,
                'offered_fob' => 0.00,
            ]);            
        
            // 3. Loop through your dynamic Alpine items array
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

            // 4. Fire the new model utility method to tally the total material costs
            $costing->updateCalculatedTotalRmCost();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Style created successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'.$e->getMessage()
            ]);
        }

    }

    public function update(Request $request, $tenant, String $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit = Style::findOrFail($id);
        
        $unit->update([
            'name' => $validated['name']
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Style updated successfully!'
            ]);
        }
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
