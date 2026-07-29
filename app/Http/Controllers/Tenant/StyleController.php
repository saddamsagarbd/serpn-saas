<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\ColorContext;
use App\Models\Season;
use App\Models\Style;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StyleController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Style::with(['buyer', 'season'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('buyer_name', function($row){
                    return $row->buyer ? $row->buyer->name : 'N/A';
                })
                ->addColumn('season_name', function($row){
                    return $row->season ? $row->season->name : 'N/A';
                })
                ->editColumn('style_name', function($row){
                    return $row->style_name ?: 'N/A';
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
        return view('tenant.inventory.style.create', compact('colors', 'units', 'buyers', 'seasons'));
    }

    public function styleStore(Request $request){
        $request->validate([
            'style_code'   => 'required|string|unique:styles,style_code',
            'style_name'   => 'required|string|max:255',
            'buyer_id'     => 'required|exists:buyers,id',
            'season_id'    => 'required|exists:seasons,id',
            'target_price' => 'nullable|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            
        
            // Style::create([
            //     'name'   => $validated['name']
            // ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Style created successfully!'
                ]);
            }
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
