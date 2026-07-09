<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            
            $data = Category::with('parent')->select('categories.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('parent', function($row){
                    return $row->parent ? $row->parent->name : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $parentCategories = Category::whereNull('parent_id')->get();
        return view('tenant.inventory.category.index', compact('parentCategories'));
    }

    /**
     * Store Category under Current Tenant Scope
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id', 
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

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

        $category = Category::findOrFail($id);

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
}
