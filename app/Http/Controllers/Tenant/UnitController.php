<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    // ইউনিট ইনডেক্স এবং অ্যালপাইন ফেড ডাটা লোড
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Unit::with('baseUnit')->select('units.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('base_unit', function($row){
                    return $row->baseUnit ? $row->baseUnit->name : 'Main Base Unit';
                })
                ->make(true);
        }

        // মডালের ড্রপডাউনে দেখানোর জন্য শুধু বেজ ইউনিটগুলো নেওয়া হচ্ছে
        $baseUnits = Unit::where('is_base_unit', true)->get();
        return view('tenant.inventory.unit.index', compact('baseUnits'));
    }

    // নতুন ইউনিট তৈরি (এজাক্স ফ্রেন্ডলি)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'is_base_unit' => 'required|boolean',
            'base_unit_id' => 'nullable|required_if:is_base_unit,0|exists:units,id',
            'operator_value' => 'nullable|required_if:is_base_unit,0|numeric',
            'operator' => 'nullable|required_if:is_base_unit,0|in:*,/',
        ]);

        $unit = Unit::create([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'is_base_unit' => $validated['is_base_unit'],
            'base_unit_id' => $validated['is_base_unit'] ? null : $validated['base_unit_id'],
            'operator_value' => $validated['is_base_unit'] ? 1.0000 : $validated['operator_value'],
            'operator' => $validated['is_base_unit'] ? '*' : $validated['operator'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully!',
                'data' => $unit
            ]);
        }

        return redirect()->back()->with('success', 'Unit created successfully!');
    }

    // ইউনিট আপডেট (এজাক্স ফ্রেন্ডলি)
    public function update(Request $request, $tenant, String $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'is_base_unit' => 'required|boolean',
            'base_unit_id' => 'nullable|required_if:is_base_unit,0|exists:units,id',
            'operator_value' => 'nullable|required_if:is_base_unit,0|numeric',
            'operator' => 'nullable|required_if:is_base_unit,0|in:*,/',
        ]);

        $unit = Unit::findOrFail($id);
        
        $unit->update([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'is_base_unit' => $validated['is_base_unit'],
            'base_unit_id' => $validated['is_base_unit'] ? null : $validated['base_unit_id'],
            'operator_value' => $validated['is_base_unit'] ? 1.0000 : $validated['operator_value'],
            'operator' => $validated['is_base_unit'] ? '*' : $validated['operator'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Unit updated successfully!');
    }

    // ইউনিট ডিলিট
    public function delete($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('success', 'Unit deleted successfully!');
    }
}