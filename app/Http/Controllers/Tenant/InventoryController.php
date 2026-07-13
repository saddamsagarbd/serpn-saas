<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ColorContext;
use App\Models\FabricSpec;
use App\Models\Item;
use App\Models\ProductionBatch;
use App\Models\Style;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function index(Request $request){
        if ($request->wantsJson() || $request->ajax()) {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $query = Item::with(['category', 'unit', 'brand', 'style'])->latest();

            // 🔍 গ্লোবাল সার্চ লজিক
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function($catQ) use ($search) {
                        $catQ->where('name', 'LIKE', "%{$search}%");
                    });
                });
            }

            // Laravel-এর বিল্ট-ইন পেজিনেটর যা শুধু ওই পেজের ১০টি ডাটা কুয়েরি করবে
            $items = $query->paginate($perPage);

            return response()->json($items);
        }
        return view('tenant.inventory.item.index');
    }
    public function itemCreate(){
        // ১. আপনার এক্সিসটিং মেটা-ডাটা কোয়েরি সমূহ
        $styles        = Style::where('status', 'active')->get();
        $fabSpec       = FabricSpec::all();
        $colorContexts = ColorContext::all();
        $brands        = Brand::where('status', 'active')->get();
        $categories    = Category::all();
        $units         = Unit::all();

        // ⚡ ২. ফ্রন্টএন্ড প্রিভিউয়ের জন্য পরবর্তী ইউনিক SKU জেনারেশন লজিক
        $currentYear = date('Y');
        $latestItem  = Item::where('sku', 'LIKE', "ITM-{$currentYear}-%")->latest('id')->first();
        $nextSequence = $latestItem ? ((int) substr($latestItem->sku, -4)) + 1 : 1;
        
        // যেমন: ITM-2026-0005
        $nextSkuPreview = 'ITM-' . $currentYear . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        return view('tenant.inventory.item.entry', compact('categories', 'units', 'styles', 'fabSpec', 'colorContexts', 'brands', 'nextSkuPreview'));
    }

    // ৩. ডাটাবেজে আইটেম মাস্টার সেভ করা
    public function itemStore(Request $request) {

        // ভ্যালিডেশন (sku ফিল্ড রিকোয়ার্ড রাখার দরকার নেই কারণ আমরা নিজেরা জেনারেট করব)
        $request->validate([            
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'style_no' => 'nullable|string',
            'fabric_code' => 'nullable|string',
            'color' => 'nullable|string',
            'brand' => 'nullable|string',
        ]);

        // 🛡️ ডাবল প্রটেকশন: ব্যাকএন্ডে রিয়েল-টাইম ইউনিক SKU জেনারেশন
        $currentYear = date('Y');
        $latestItem  = Item::where('sku', 'LIKE', "ITM-{$currentYear}-%")->latest('id')->first();
        $nextSequence = $latestItem ? ((int) substr($latestItem->sku, -4)) + 1 : 1;
        
        $finalSku = 'ITM-' . $currentYear . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $purchasePrice = floatval($request->purchase_price ?? 0);
        $salePrice     = floatval($request->sale_price ?? 0);

        $rowsToInsert = [
            'sku'              => $finalSku,
            'name'             => trim($request->name),
            'style_id'         => trim($request->style_id),
            'fabric_spec_id'   => $request->fabric_code, 
            'color_context_id' => $request->color,      
            'brand_id'         => $request->brand,
            'category_id'      => trim($request->category_id),
            'unit_id'          => trim($request->unit_id ?? 1), // ডাটাবেজে UOM না মিললে ডিফল্ট ID: 1
            'purchase_price'   => $purchasePrice,
            'sale_price'       => $salePrice,
            'stock_qty'        => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        // ডাটাবেজে ডাটা সেভ
        Item::create($rowsToInsert);

        return redirect()->route('tenant.inventory.items.index')->with('success', 'Master Item registered perfectly.');
    }

    public function itemedit($tenant, string $id)
    {
        // ১. নির্দিষ্ট আইটেমটি খুঁজে বের করা (যদি না পায় তবে 404 ইরর দিবে)
        $item = Item::findOrFail($id);
        
        // ২. ফর্মের ড্রপডাউনে দেখানোর জন্য বাকি মেটা-ডাটা লোড করা
        $styles        = Style::all();
        $fabSpec       = FabricSpec::all();
        $colorContexts = ColorContext::all();
        $brands        = Brand::all();
        $categories    = Category::all();
        $units         = Unit::all();

        // ভিউ ফাইলে ডাটা পাস করা (ধরে নিচ্ছি আপনার ব্লেড ফাইলটি edit.blade.php নামে আছে)
        return view('tenant.inventory.item.entry', compact(
            'item', 'styles', 'fabSpec', 'colorContexts', 'brands', 'categories', 'units'
        ));
    }

    public function itemupdate($tenant, Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        $item = Item::findOrFail($id);

        $item->update([
            'name' => $request->name,
            'style_id' => $request->style_id,
            'fabric_spec_id' => $request->fabric_code,
            'color_context_id' => $request->color,
            'brand_id' => $request->brand,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
        ]);

        return redirect()->route('tenant.inventory.items.index')
            ->with('success', 'Master Item configurations updated perfectly.');
    }

    public function downloadSampleCsv(): StreamedResponse
    {
        // ইউজারের বোঝার সুবিধার্থে ডাটাবেজ থেকে ১টি করে স্যাম্পল ভ্যালু তুলে আনা (যদি থাকে)
        $sampleStyle = Style::first()?->name ?? 'NOVOJKT-02';
        $sampleFabric = FabricSpec::first()?->name ?? '100% Cotton 180 GSM';
        $sampleColor = ColorContext::first()?->name ?? 'Black';
        $sampleBrand = Brand::first()?->name ?? 'Levi\'s';
        $sampleCat   = Category::first()?->name ?? 'T-Shirt';
        $sampleUnit  = Unit::first()?->short_name ?? 'pcs'; // Short name matching mandatory

        // CSV হেডার আর্কিটেকচার
        $headers = [
            'Item Name',
            'Style',
            'Fabric',
            'Color',
            'Brand',
            'Category',
            'UOM',
            'Purchase Price',
            'Sale Price'
        ];

        // স্যাম্পল গাইডলাইন ডেটা রো
        $sampleData = [
            'Premium Crewneck T-Shirt',
            $sampleStyle,
            $sampleFabric,
            $sampleColor,
            $sampleBrand,
            $sampleCat,
            $sampleUnit,
            '250.00',
            '450.00'
        ];

        $response = new StreamedResponse(function () use ($headers, $sampleData) {
            $handle = fopen('php://output', 'w');
            
            // হেডার এবং স্যাম্পল ডেটা রাইট করা
            fputcsv($handle, $headers);
            fputcsv($handle, $sampleData);
            
            fclose($handle);
        });

        // ফাইল ডাউনলোড রেসপন্স হেডার সেটআপ
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="master_item_import_template.csv"');

        return $response;
    }

    public function importCsv(Request $request){
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // ১ম লাইন (হেডার) স্কিপ করা
        fgetcsv($handle, 1000, ',');

        $units = Unit::pluck('id', 'short_name')->toArray();

        $currentYear = date('Y');

        $latestItem = Item::where('sku', 'LIKE', "ITM-{$currentYear}-%")->latest('id')->first();
        $nextSequence = $latestItem ? ((int) substr($latestItem->sku, -4)) + 1 : 1;

        $rowsToInsert = [];
        $rowCount = 0;

        DB::beginTransaction();

        try {

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (empty($row) || count($row) < 2) continue;

                // CSV কলাম ম্যাপিং
                $name          = trim($row[0]);
                $styleName     = !empty($row[1]) ? trim($row[1]) : null;
                $fabricName    = !empty($row[2]) ? trim($row[2]) : null;
                $colorName     = !empty($row[3]) ? trim($row[3]) : null;
                $brandName     = !empty($row[4]) ? trim($row[4]) : null;
                $categoryName  = trim($row[5]);
                $uomShortName  = trim($row[6]);
                $purchasePrice = floatval($row[7] ?? 0);
                $salePrice     = floatval($row[8] ?? 0);

                // ⚡ ডাইনামিক অটো-ক্রিয়েশন পাইপলাইন (First or Create)
                
                // ১. ক্যাটাগরি না থাকলে অটো ক্রিয়েট হবে
                $category = Category::firstOrCreate(['name' => $categoryName]);

                // ২. ব্র্যান্ড না থাকলে অটো ক্রিয়েট হবে
                $brandId = null;
                if ($brandName) {
                    $brand = Brand::firstOrCreate(['name' => $brandName]);
                    $brandId = $brand->id;
                }

                // ৩. স্টাইল না থাকলে অটো ক্রিয়েট হবে
                $styleId = null;
                if ($styleName) {
                    // আমরা আগে যে ইউনিক কোড জেনারেটরের লজিক লিখেছিলাম, তা মডেল বুট ইভেন্টে থাকলে শুধু নাম দিলেই হবে
                    $style = Style::firstOrCreate(['name' => $styleName]);
                    $styleId = $style->id;
                }

                // ৪. ফ্যাব্রিক স্পেসিফিকেশন না থাকলে অটো ক্রিয়েট হবে
                $fabricId = null;
                if ($fabricName) {
                    $fabric = FabricSpec::firstOrCreate(['name' => $fabricName]);
                    $fabricId = $fabric->id;
                }

                // ৫. কালার কনটেক্সট না থাকলে অটো ক্রিয়েট হবে
                $colorId = null;
                if ($colorName) {
                    // কালার কোড ডিফল্ট হিসেবে একটা র্যান্ডম বা গ্রে কালার দিয়ে ক্রিয়েট হবে যা পরে ইউজার এডিট করতে পারবে
                    $color = ColorContext::firstOrCreate(
                        ['name' => $colorName],
                        ['color_code' => '#64748b'] 
                    );
                    $colorId = $color->id;
                }

                // ⚡ অটো-সিকোয়েন্সিয়াল SKU জেনারেশন
                $generatedSku = 'ITM-' . $currentYear . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
                $nextSequence++;

                $rowsToInsert[] = [
                    'sku'              => $generatedSku,
                    'name'             => $name,
                    'style_id'         => $styleId,
                    'fabric_spec_id'   => $fabricId, 
                    'color_context_id' => $colorId,      
                    'brand_id'         => $brandId,
                    'category_id'      => $category->id,
                    'unit_id'          => $units[$uomShortName] ?? 1, // ডাটাবেজে UOM না মিললে ডিফল্ট ID: 1
                    'purchase_price'   => $purchasePrice,
                    'sale_price'       => $salePrice,
                    'stock_qty'        => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                $rowCount++;
            }
            fclose($handle);
            // বাল্ক ডাটা ইনসার্ট
            if (count($rowsToInsert) > 0) {
                Item::insert($rowsToInsert);
            }

            DB::commit();
            return redirect()->route('tenant.inventory.items.index')
                ->with('success', "{$rowCount} Items successfully synchronized along with automatic meta-data creation.");
        }catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'CSV Safe-Ingest Failed: ' . $e->getMessage()]);
        }

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