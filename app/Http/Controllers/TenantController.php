<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index(){
        $plans = Plan::where('is_active', true)->get();
        $tenants = Tenant::all(); // with('domains') সাময়িকভাবে বাদ দেওয়া হলো রিলেশন এরর এড়াতে
        return view('tenants', compact('tenants', 'plans'));
    }

    public function store(Request $request) {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|unique:tenants,owner_email',
            'owner_phone'  => 'required|string|max:20',
            'plan_id'      => 'required|exists:plans,id', // আমরা পূর্বে যে প্ল্যান তৈরি করেছি
        ]);

        DB::beginTransaction();
        try {
            Tenant::create([
                'plan_id'         => $request->plan_id,
                'company_name'    => $request->company_name,
                'owner_name'      => $request->owner_name,
                'owner_email'     => $request->owner_email,
                'owner_phone'     => $request->owner_phone,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'নতুন Tenant এবং Vendor Admin সফলভাবে অ্যাক্টিভেট হয়েছে!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'টেন্যান্ট তৈরি করা যায়নি: ' . $e->getMessage()]);
        }
    }
}
