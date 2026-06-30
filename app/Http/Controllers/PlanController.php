<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function index() {
        $plans = Plan::all(); // with('domains') সাময়িকভাবে বাদ দেওয়া হলো রিলেশন এরর এড়াতে
        return view('plans', compact('plans'));

    }

    public function store(Request $request) {
        // ১. ভ্যালিডেশন (ডাটা স্ট্রাকচার অনুযায়ী nested keys ব্যবহার করা হয়েছে)
        $request->validate([
            'plan.title'             => 'required|string|max:255',
            'plan.code'              => 'required|string|unique:plans,code',
            'plan.price'             => 'required|numeric|min:0',
            'plan.billing_period'    => 'required|string|in:month,year',
            'plan.max_product_limit' => 'required|integer|min:-1',
            'plan.max_invoice_limit' => 'required|integer|min:-1',
            'plan.best_deal'         => 'nullable|boolean', // যদি ফর্মে চেকবক্স থাকে
            'features'               => 'nullable|array',
        ]);

        // DB Transaction শুরু
        DB::beginTransaction();

        try {
            // লারাভেলে $request->input() ব্যবহার করলে অ্যারে হ্যান্ডেল করা সহজ এবং নিরাপদ হয়
            $planData = $request->input('plan');
            $features = $request->input('features', []);

            $plan = new Plan();

            // অ্যারে নোটেশন ['$key'] ব্যবহার করে ট্রিম করা হলো
            $plan->title             = trim($planData['title']);
            $plan->code              = strtolower(trim($planData['code'])); // কোড সবসময় ছোট হাতের রাখা ভালো
            $plan->price             = trim($planData['price']);
            $plan->billing_period    = trim($planData['billing_period']);
            $plan->max_product_limit = trim($planData['max_product_limit']);
            $plan->max_invoice_limit = trim($planData['max_invoice_limit']);
            
            // best_deal চেকবক্স ফাঁকা থাকলে ডিফল্ট ০ বা false সেভ হবে
            $plan->best_deal         = isset($planData['best_deal']) ? (bool)$planData['best_deal'] : false;
            
            // মডেল ফাইলে যদি protected $casts = ['features' => 'array'] করা থাকে, 
            // তবে json_encode করার দরকার নেই, লারাভেল নিজেই করবে। শুধু $features অ্যাসাইন করলেই হবে।
            $plan->features          = $features; 

            $plan->save();

            // সব ঠিক থাকলে ডাটাবেজে পার্মানেন্টলি সেভ করো
            DB::commit();

            return redirect()->back()->with([
                'success' => 'নতুন Plan সফলভাবে তৈরি হয়েছে!',
                'currentTab' => 'plans' // এরর ছাড়া সাবমিট হলেও যেন প্ল্যান ট্যাবটাই ওপেন থাকে
            ]);

        }catch(\Exception $e) {
            // কোনো এরর হলে ডাটাবেজের সব রোলব্যাক (বাতিল) হবে
            DB::rollBack();

            // ব্যাকএন্ড লগে এররটি সেভ করে রাখা (ভবিষ্যতে ডিবাগিংয়ের জন্য)
            Log::error('Plan Creation Failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput() // ইউজার যা ইনপুট দিয়েছিল তা ফর্মে ধরে রাখবে
                ->withErrors(['error' => 'সিস্টেম এরর! প্ল্যান তৈরি করা যায়নি। দয়া করে আবার চেষ্টা করুন।']);
        }

    }
}
