<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ColorContext;
use App\Models\FabricSpec;
use App\Models\ItemMaster;
use App\Models\ItemVarient;
use App\Models\ProductionBatch;
use App\Models\Stock;
use App\Models\Style;
use App\Models\Unit;
use Carbon\Carbon;
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

            $query = ItemMaster::with(['category', 'unit', 'brand', 'style'])->latest();

            // 🔍 গ্লোবাল সার্চ লজিক
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            // Laravel-এর বিল্ট-ইন পেজিনেটর যা শুধু ওই পেজের ১০টি ডাটা কুয়েরি করবে
            $items = $query->paginate($perPage);

            return response()->json($items);
        }
        return view('tenant.inventory.item.index');
    }
    public function itemCreate(){
        $units         = Unit::all();
        $categories    = Category::where('is_active', 1)->get();

        $currentYear = date('Y');
        $latestItem  = ItemVarient::where('sku', 'LIKE', "ITM-{$currentYear}-%")->latest('id')->first();
        $nextSequence = $latestItem ? ((int) substr($latestItem->sku, -4)) + 1 : 1;
        
        // যেমন: ITM-2026-0005
        $nextSkuPreview = 'ITM-' . $currentYear . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        return view('tenant.inventory.item.entry', compact('units', 'nextSkuPreview', 'categories'));
    }

    // ৩. ডাটাবেজে আইটেম মাস্টার সেভ করা
    public function itemStore(Request $request) {

        $request->validate([            
            'item_name' => 'required|string|max:255',
            'item_type' => 'required|string',
        ]);

        $tenantId = tenant('id');
        $now = Carbon::now();
        $currentUser = auth()->id();

        $typeLower = strtolower(trim($request->item_type));
    
        $prefix = match ($typeLower) {
            'fabrics'         => 'FAB',
            'trims'          => 'TRM',
            'accessories'    => 'ACC',
            'chemical'       => 'CHM',
            'finished-goods' => 'FG',
            default          => 'ITM',
        };

        if (empty($request->item_code)) {
            $nextNumber = ItemMaster::where('tenant_id', $tenantId)
                ->where('item_type', $typeLower)
                ->count() + 1;

            $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $itemCode = "{$prefix}-" . date('Y') . "-{$sequence}";
        } else {
            $itemCode = $request->item_code;
        }       

        DB::beginTransaction();
        try {

            ItemMaster::create([
                'tenant_id'   => $tenantId,
                'code'        => $itemCode,
                'name'        => $request->item_name,
                'item_type'   =>  $typeLower,
                'unit_id'     => $request->unit_id,
                'category_id'     => $request->category_id,
                'created_by'     => $currentUser,
                'created_at'     => $now,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message'=> 'Master Item registered perfectly.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message'=> 'Something went wrong. Error is:'.$e->getMessage()]);
        }

        
    }

    public function itemedit($tenant, string $id)
    {
        // ১. নির্দিষ্ট আইটেমটি খুঁজে বের করা (যদি না পায় তবে 404 ইরর দিবে)
        $item = ItemMaster::findOrFail($id);
        
        // ২. ফর্মের ড্রপডাউনে দেখানোর জন্য বাকি মেটা-ডাটা লোড করা
        $units         = Unit::all();

        $categories    = Category::where('is_active', 1)->get();

        // ভিউ ফাইলে ডাটা পাস করা (ধরে নিচ্ছি আপনার ব্লেড ফাইলটি edit.blade.php নামে আছে)
        return view('tenant.inventory.item.entry', compact(
            'units', 'item', 'categories'
        ));
    }

    public function itemupdate($tenant, Request $request, string $id)
    {
        $validated = $request->validate([            
            'item_name' => 'required|string|max:255',
            'item_type' => 'required|string',
            'unit_id'   => 'required|integer|exists:units,id',
            'category_id'   => 'required|integer|exists:categories,id',
        ]);

        $now = Carbon::now();        
        $currentUser = auth()->id();

        $item = ItemMaster::findOrFail($id);

        $typeLower = strtolower(trim($request->item_type));

        try {

            $item->update([
                'name'      => $validated['item_name'],
                'item_type' => $typeLower,
                'unit_id'   => $validated['unit_id'],
                'category_id'   => $validated['category_id'],
                'updated_by' => $currentUser,
                'updated_at' => $now,
            ]);

            return response()->json(['success' => true, 'message'=> 'Master Item updated perfectly.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message'=> 'Something went wrong. Error is:'.$e->getMessage()]);
        }
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

        $stocks = Stock::where('tenant_id', tenant('id'))
        ->with(['itemVariant', 'warehouse'])
        ->get();

        $items = ItemMaster::where('tenant_id', tenant('id'))
            ->with(['category', 'unit', 'brand', 'style', 'color'])
            ->get();

        return view('tenant.inventory.stock.index', compact('stocks', 'items'));
    }

    public function storeStock(Request $request) {
        dd($request->all());
    }

    public function stockEntry() {
        $items = ItemMaster::orderBy('name', 'asc')->get();
        $batches = ProductionBatch::with('item')->latest()->get();
        return view('tenant.inventory.stock.entry', compact('items', 'batches'));
    }
    // Batch Production Form Submission (Handles index.blade.php functionality)
    // public function storeBatchProduction(Request $request) {
    //     $request->validate([
    //         'item_id' => 'required|exists:item_masters,id',
    //         'production_date' => 'required|date',
    //         'quantity' => 'required|integer|min:1',
    //     ]);

    //     return DB::transaction(function () use ($request) {
    //         $batchNo = 'BAT-' . date('Ymd') . '-' . rand(10, 99);
            
    //         ProductionBatch::create([
    //             'item_id' => $request->item_id,
    //             'batch_no' => $batchNo,
    //             'production_date' => $request->production_date,
    //             'quantity' => $request->quantity,
    //             'barcode_start' => '#001',
    //             'barcode_end' => '#' . str_pad($request->quantity, 3, '0', STR_PAD_LEFT)
    //         ]);

    //         $stock = Stock::findOrFail($request->item_id);
    //         $stock->increment('stock_qty', $request->quantity);

    //         return redirect()->back()->with('success', "Batch $batchNo Registered & Stock Levels Updated.");
    //     });
    // }
    public function barcode() {
        return view('tenant.inventory.stock.barcode');
    }

    public function searchApi(Request $request)
    {
        $search = $request->input('q');
        $cat_id = $request->input('cat_id');

        $query = ItemMaster::where('tenant_id', tenant('id'));
        if(!empty($search)){
            $query->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('code', 'LIKE', "%{$search}%");
            });            
        }

        if(!empty($cat_id)){
            $query->when($search, function ($query, $cat_id) {
                return $query->where('category_id', 'LIKE', "%{$cat_id}%");
            });            
        }

        $items = $query->get(['id', 'code', 'name', 'item_type']);

        $results = $items->map(function ($item) {
            $name = $item->name ?? 'Unnamed Item';
            $code = $item->code ? '[' . $item->code . '] ' : '';
            return [
                'id' => $item->id,
                'text' => $code . $name,
                'name' => $name,
                'item_type' => strtolower($item->item_type ?? '') === 'fabrics' ? 'fabrics' : 'trim'
            ];
        });

        return response()->json(['results' => $results]);
    }
    public function searchCategoryApi(Request $request)
    {
        $search = $request->input('q');

        $query = Category::where('tenant_id', tenant('id'));
        if(!empty($search)){
            $query->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('code', 'LIKE', "%{$search}%");
            });            
        }
        $categories = $query->get(['id', 'code', 'name']);

        $results = $categories->map(function ($cat) {
            $name = $cat->name ?? 'Unnamed Item';
            $code = $cat->code ? '[' . $cat->code . '] ' : '';
            return [
                'id'    => $cat->id,
                'text'  => $code . $name,
                'name'  => $name,
            ];
        });

        return response()->json(['results' => $results]);
    }
}