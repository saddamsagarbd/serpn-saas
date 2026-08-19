<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            
            $data = Category::with('parent')->where('tenant_id', tenant('id'))->select('categories.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('parent', function($row){
                    return $row->parent ? $row->parent->name : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tenant.inventory.category.index');
    }

    /**
     * Store Category under Current Tenant Scope
     */
    public function store($tenant, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('tenant_id', tenant('id'));
                }),
            ], 
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = tenant('id');

        $category = Category::create($validated);

        // AJAX / Fetch Request হলে JSON Return করুন
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category saved inside current tenant vault.',
                'category' => $category
            ], 201);
        }

        return redirect()->back()->with('success', 'Category saved inside current tenant vault.');
    }

    /**
     * Update Category with strict Parent-Child prevention check
     */
    public function update(Request $request, $tenant, String $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $category = Category::where('tenant_id', tenant('id'))->findOrFail($id);

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'],
            'description' => $validated['description'] ?? null,
        ]);

        // এজাক্স রিকোয়েস্টের জন্য জেসন রিটার্ন (এটি পেজ রিফ্রেশ রোধ করবে)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function getParentCategories()
    {
        $parents = Category::where('tenant_id', tenant('id'))
            ->whereNull('parent_id')
            ->select('id', 'name')
            ->get();

        return response()->json($parents);
    }
}
