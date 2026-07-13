<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ColorContext;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ColorContextController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = ColorContext::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return $row->name ? $row->name : 'N/A';
                })
                ->editColumn('code', function($row){
                    return $row->color_code ? $row->color_code : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tenant.inventory.color-context.index');
    }

    public function colorStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        ColorContext::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Color context created successfully!'
            ]);
        }

    }

    public function update(Request $request, $tenant, String $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cContext = ColorContext::findOrFail($id);
        
        $cContext->update([
            'name' => trim($request->name),
            'color_code'   => $request->color_code ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Color context updated successfully!'
            ]);
        }
    }
}
