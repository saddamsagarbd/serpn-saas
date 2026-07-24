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
    /**
     * Business types recognized by the menu resolver (menu.php).
     * Kept as a single source of truth for validation here.
     */
    private const BUSINESS_TYPES = ['merchandising', 'real_estate', 'general_retail'];

    public function index(Request $request){
        $plans = Plan::where('is_active', true)->get();
        $tenants = Tenant::with(['domains', 'plan'])->latest()->get();
        $businessTypes = collect(TenantController::BUSINESS_TYPES)->map(function ($type) {
            return [
                'value' => $type,
                'label' => str($type)->replace('_', ' ')->title()->toString(), // E.g., 'Real Estate'
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $tenants->map(function ($tenant) {
                    return [
                        'id'            => $tenant->id,
                        'company_name'  => $tenant->company_name,
                        'subdomain'     => optional($tenant->domains->first())->domain,
                        'business_type' => $tenant->business_type,
                        'owner_name'    => $tenant->owner_name,
                        'owner_email'   => $tenant->owner_email,
                        'owner_phone'   => $tenant->owner_phone,
                        'plan_id'       => $tenant->plan_id, // Pass raw ID for form bindings
                        'plan'          => optional($tenant->plan)->title . " - (" . optional($tenant->plan)->price . "/" . optional($tenant->plan)->billing_period . ")",
                        'status'        => $tenant->status,
                    ];
                }),
            ]);
        }

        return view('tenants', compact('tenants', 'plans', 'businessTypes'));

    }

    public function store(Request $request) 
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|unique:tenants,owner_email',
            'owner_phone'  => 'required|string|max:20',
            'business_type' => 'required|in:' . implode(',', self::BUSINESS_TYPES),
            'plan'      => 'required|exists:plans,id',
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
                'plan_id'      => $request->plan,
                'company_name' => trim($request->company_name),
                'owner_name'   => trim($request->owner_name),
                'owner_email'  => strtolower(trim($request->owner_email)),
                'owner_phone'  => trim($request->owner_phone),
                'business_type'  => $request->business_type,
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
                    'plan_id'       => $tenant->plan,
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

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'company_name'  => 'required|string|max:255',
            'owner_name'    => 'required|string|max:255',
            // unique check ignores this tenant's own current email
            'owner_email'   => 'required|email|unique:tenants,owner_email,' . $tenant->id . ',id',
            'owner_phone'   => 'required|string|max:20',
            'plan'       => 'required|exists:plans,id',
            'business_type' => 'required|in:' . implode(',', self::BUSINESS_TYPES),
            'status'        => 'nullable|in:active,suspended',
        ]);

        

        try {
            // Deliberately NOT touching 'id' / domain slug on update — the
            // subdomain is fixed at creation time. Renaming the company here
            // does not rename the tenant's live domain.
            $updateParam = [
                'company_name'  => trim($request->company_name),
                'owner_name'    => trim($request->owner_name),
                'owner_email'   => strtolower(trim($request->owner_email)),
                'owner_phone'   => trim($request->owner_phone),
                'plan_id'       => $request->plan,
                'business_type' => $request->business_type,
                'status'        => $request->status ?? "active",
            ];

            $tenant->update($updateParam);

            // Keep the subscription record's plan_id in sync if it changed.
            tenancy()->central(function () use ($tenant, $request) {
                DB::table('tenant_subscriptions')
                    ->where('tenant_id', $tenant->id)
                    ->update([
                        'plan_id'    => $request->plan_id,
                        'updated_at' => Carbon::now(),
                    ]);
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tenant updated successfully.']);
            }

            return redirect()->back()->with('success', 'Tenant updated successfully.');

        } catch (\Exception $e) {
            Log::error('Tenant Update Fatal Error: ' . $e->getMessage());

            return response()->json(['message' => 'Tenant update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * "Delete" a tenant — per requirement, this NEVER hard-deletes the
     * tenant record or its isolated database. It only flips status to
     * 'suspended', which should be enforced at the tenancy middleware
     * level to block login/access for suspended tenants.
     */
    public function destroy(Request $request, Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'suspended']);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tenant suspended. Data and subdomain remain intact.']);
            }

            return redirect()->back()->with('success', 'Tenant suspended. Data and subdomain remain intact.');

        } catch (\Exception $e) {
            Log::error('Tenant Suspend Fatal Error: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to suspend tenant: ' . $e->getMessage()], 500);
        }
    }


}