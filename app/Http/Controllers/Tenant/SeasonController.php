<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SeasonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Season::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
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

        Season::create($request->all());

        return response()->json(['success' => true, 'message' => 'Season created successfully.']);
    }
}
