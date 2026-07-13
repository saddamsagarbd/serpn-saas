<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\FabricSpec;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FebricController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = FabricSpec::all();

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
        return view('tenant.inventory.febric.index');
    }

    public function styleStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        FabricSpec::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fabric spec created successfully!'
            ]);
        }

    }

    public function update(Request $request, $tenant, String $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit = FabricSpec::findOrFail($id);
        
        $unit->update([
            'name' => trim($request->name),
            'gsm'   => $request->gsm ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fabric spec updated successfully!'
            ]);
        }
    }
}
