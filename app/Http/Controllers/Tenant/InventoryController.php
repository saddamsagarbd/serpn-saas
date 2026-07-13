<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\ProductionBatch;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function brands(Request $request){
        if ($request->ajax()) {
            $data = Brand::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return $row->name ? $row->name : 'N/A';
                })
                ->editColumn('code', function($row){
                    return $row->code ? $row->code : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tenant.inventory.brand.index');
    }

    public function brandStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        Brand::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully!'
            ]);
        }
    }

    public function updateBrand(Request $request, $tenant, String $id){
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cContext = Brand::findOrFail($id);
        
        $cContext->update([
            'name' => trim($request->name),
            'color_code'   => $request->color_code ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully!'
            ]);
        }
    }

    public function index(){
        $items = Item::with(['category', 'unit'])->latest()->get();
        return view('tenant.inventory.item.index', compact('items'));
    }
    public function itemCreate(){
        $categories = Category::all();
        $units = Unit::all();
        return view('tenant.inventory.item.entry', compact('categories', 'units'));
    }

    // ৩. ডাটাবেজে আইটেম মাস্টার সেভ করা
    public function itemStore(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:items,sku',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'style_no' => 'nullable|string',
            'fabric_code' => 'nullable|string',
            'color' => 'nullable|string',
            'brand' => 'nullable|string',
        ]);

        Item::create($validated);

        return redirect()->route('tenant.inventory.item.index')->with('success', 'Master Product/Component successfully added to Catalog Registry.');
    }

    public function stock() {

        $stocks = Item::with(['category', 'unit'])->orderBy('stock_qty', 'desc')->get();

        return view('tenant.inventory.stock.index', compact('stocks'));
    }

    public function stockEntry() {
        $items = Item::orderBy('name', 'asc')->get();
        $batches = ProductionBatch::with('item')->latest()->get();
        return view('tenant.inventory.stock.entry', compact('items', 'batches'));
    }
    // Batch Production Form Submission (Handles index.blade.php functionality)
    public function storeBatchProduction(Request $request) {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'production_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $batchNo = 'BAT-' . date('Ymd') . '-' . rand(10, 99);
            
            ProductionBatch::create([
                'item_id' => $request->item_id,
                'batch_no' => $batchNo,
                'production_date' => $request->production_date,
                'quantity' => $request->quantity,
                'barcode_start' => '#001',
                'barcode_end' => '#' . str_pad($request->quantity, 3, '0', STR_PAD_LEFT)
            ]);

            $item = Item::findOrFail($request->item_id);
            $item->increment('stock_qty', $request->quantity);

            return redirect()->back()->with('success', "Batch $batchNo Registered & Stock Levels Updated.");
        });
    }
    public function barcode() {
        return view('tenant.inventory.stock.barcode');
    }
}