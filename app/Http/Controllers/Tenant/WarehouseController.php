<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Warehouse::where('tenant_id', tenant('id'))->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function($row){
                    return $row->code ? $row->code : 'N/A';
                })
                ->addColumn('name', function($row){
                    return $row->name ? $row->name : 'N/A';
                })
                ->editColumn('address', function($row){
                    return $row->location ?: 'N/A';
                })
                ->editColumn('is_default', function($row){
                    return $row->is_default==1 ?'<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">Default</span>': 'N/A';
                })
                ->rawColumns(['is_default'])
                ->make(true);
        }
        return view('tenant.warehouse.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "location"           => trim($request->address),
                "is_default"        => $request->isDefault ?? false,
            ];

            Warehouse::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Warehouse created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }

    public function update(Request $request, $tenant, String $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $warehouse = Warehouse::findOrFail($id);

            $warehouse->update([
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "location"           => trim($request->address),
                "is_default"        => $request->isDefault ?? false,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Warehouse updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }
}
