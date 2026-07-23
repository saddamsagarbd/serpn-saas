<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ColorContext;
use App\Models\Style;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StyleController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Style::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return $row->name ? $row->name : 'N/A';
                })
                ->editColumn('code', function($row){
                    return $row->code ? $row->code : 'Unlink';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tenant.inventory.style.index');
    }
    public function createStyle(){
        $colors = ColorContext::all();
        $units = Unit::all();
        return view('tenant.inventory.style.create', compact('colors', 'units'));
    }

    public function styleStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        Style::create([
            'name'   => $validated['name']
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Style created successfully!'
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
