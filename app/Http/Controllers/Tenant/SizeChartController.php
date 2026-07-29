<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SizeChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SizeChartController extends Controller
{
    // ইউনিট ইনডেক্স এবং অ্যালপাইন ফেড ডাটা লোড
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SizeChart::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('base_unit', function($row){
                    return $row->baseUnit ? $row->baseUnit->name : 'Main Base Unit';
                })
                ->make(true);
        }
        return view('tenant.inventory.size-chart.index');
    }

    // নতুন ইউনিট তৈরি (এজাক্স ফ্রেন্ডলি)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "short_name"        => trim($request->short_name),
            ];

            SizeChart::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Size chart successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }

    // ইউনিট আপডেট (এজাক্স ফ্রেন্ডলি)
    public function update(Request $request, $tenant, String $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $size = SizeChart::findOrFail($id);
            $size->update([
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "short_name"        => trim($request->short_name),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Size chart updated.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }
}
