<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Buyer::with(['country'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    $search = $request->input('search.value');
                    
                    if (!empty($search)) {
                        $instance->where(function($w) use ($search) {
                            $w->where('buyers.name', 'LIKE', "%{$search}%")
                            ->orWhere('buyers.email', 'LIKE', "%{$search}%")
                            ->orWhere('buyers.contact_person', 'LIKE', "%{$search}%");
                            
                            // ক্রস-ডাটাবেজ রিলেশনাল সার্চ ফিক্স
                            $w->orWhereHas('country', function($q) use ($search) {
                                // সেন্ট্রাল কানেকশনের ডাটাবেজ নাম এবং টেবিলের নাম একসাথে জোড়া দেওয়া (যেমন: central_db.countries)
                                $centralDb = config('database.connections.mysql.database'); // আপনার সেন্ট্রাল কানেকশনের নাম
                                
                                $q->from($centralDb . '.countries')
                                ->where('name', 'LIKE', "%{$search}%");
                            });
                        });
                    }
                })
                ->editColumn('name', function($row){ 
                    return $row->name ? html_entity_decode($row->name) : 'N/A'; 
                })
                ->editColumn('country', function($row){ 
                    // ডাইনামিক অবজেক্ট রিলেশন সেফলি চেক করা
                    return ($row->country && is_object($row->country)) ? $row->country->name : 'N/A';
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
        $response = World::countries();
    
        $countryList = [];
        if ($response->success) {
            $countryList = $response->data;
            if (is_object($countryList) && method_exists($countryList, 'toArray')) {
                $countryList = $countryList->toArray();
            }
        }

        return view('tenant.buyers.index', compact('countryList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "country_id"        => trim($request->country),
                "contact_person"    => trim($request->contact_person),
                "email"             => trim($request->email),
                "created_by"        => auth()->id(),
            ];

            Buyer::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Buyer created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }

    public function update(Request $request, $tenant, String $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();

        try {

            $buyer = Buyer::findOrFail($id);

            $buyer->update([
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "country_id"        => trim($request->country),
                "contact_person"    => trim($request->contact_person),
                "email"             => trim($request->email),
                "updated_by"        => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Buyer created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }
}