<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index(){
        $plans = Plan::where('is_active', true)->get();
        $tenants = Tenant::all(); // with('domains') সাময়িকভাবে বাদ দেওয়া হলো রিলেশন এরর এড়াতে
        return view('tenants', compact('tenants', 'plans'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|unique:tenants,owner_email',
            'owner_phone'  => 'required|string|max:20',
            'plan_id'      => 'required|exists:plans,id',
        ]);

        try {
            $now = Carbon::now();

            // ১. স্লুগ তৈরি করা
            $baseSlug = strtolower(str_replace([" ", "-"], "", $request->company_name));
            $domainPrefix = $baseSlug;
            $counter = 1;

            $reserved = ['localhost', 'admin', 'superadmin', 'api', 'central', '127-0-0-1'];
            while (Tenant::where('id', $domainPrefix)->exists() || in_array($domainPrefix, $reserved)) {
                $domainPrefix = $baseSlug . $counter;
                $counter++;
            }
            
            // ২. Tenant তৈরি করা (এটি ডাটাবেজ তৈরি ও মাইগ্রেশন অটোমেটিক কমপ্লিট করবে)
            $tenantParams = [
                'id'           => $domainPrefix, 
                'plan_id'      => $request->plan_id,
                'company_name' => trim($request->company_name),
                'owner_name'   => trim($request->owner_name),
                'owner_email'  => strtolower(trim($request->owner_email)),
                'owner_phone'  => trim($request->owner_phone),
            ];

            // 🚀 ৩. প্যাকেজের অফিশিয়াল ইভেন্ট মেকানিজম (ফ্রেশ ও অটোমেটিক)
            // $tenant = new Tenant($tenantParams);
            
            $tenant = Tenant::create($tenantParams);

            sleep(5);

            // ৪. সেন্ট্রাল কনটেক্সটে ফিরে গিয়ে সাবস্ক্রিপশন রেকর্ড করা
            // (যেহেতু টেন্যান্ট ক্রিয়েশনের পর লারাভেল টেন্যান্ট ডিবি-তে থাকে, তাই সেন্ট্রাল ডিবি-তে লিখতে এটি দরকার)
            tenancy()->central(function () use ($tenant, $domainPrefix, $now) {
                // ৩. ডোমেইন তৈরি করা
                $centralDomain = config('tenancy.central_domains')[0] ?? 'serpn-saas.test';
                $fullDomain = $domainPrefix . '.' . $centralDomain;

                $tenant->domains()->create([
                    'domain' => $fullDomain
                ]);
            
                DB::table("tenant_subscriptions")->insert([
                    'tenant_id'     => $tenant->id,
                    'plan_id'       => $tenant->plan_id,
                    'trial_ends_at' => $now->copy()->addMonths(1),
                    'starts_at'     => $now->copy()->addMonths(1),
                    'ends_at'       => $now->copy()->addMonths(13),
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            });

            return redirect()->back()->with('success', 'নতুন Tenant এবং Vendor Admin সফলভাবে অ্যাক্টিভেট হয়েছে!');

        } catch (\Exception $e) {
            Log::error('Tenant Creation Fatal Error: ' . $e->getMessage());
            
            // ডিবাগিং সহজ করার জন্য সরাসরি স্ক্রিনে এরর প্রিন্ট করবে (টেস্টিং পিরিয়ডে)
            dd([
                'Error Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile()
            ]);
        }
    }
}