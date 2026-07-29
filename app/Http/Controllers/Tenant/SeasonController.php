<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SeasonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Season::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    $search = $request->input('search.value');
                    
                    if (!empty($search)) {
                        $instance->where(function($w) use ($search) {
                            $w->where('seasons.name', 'LIKE', "%{$search}%");
                        });
                    }
                })
                ->editColumn('name', function($row){ return $row->name ?: 'N/A'; })
                ->make(true);
        }
        return view('tenant.seasons.index');
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
                "created_by"        => auth()->id(),
            ];

            Season::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Season created successfully.']);

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

            $season = Season::findOrFail($id);

            $season->update([
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "updated_by"        => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Season updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }
}
