<?php

namespace App\Livewire\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    #[Layout('layouts.tenant')]
    public function render()
    {
        // ১. টেন্যান্টের নিজস্ব ডাটাবেজ থেকে ডাটা ক্যালকুলেট করা (আপনার টেবিলের নাম অনুযায়ী অ্যাডজাস্ট করে নেবেন)
        
        // আজকের মোট বিক্রি (কাল্পনিক টেবিল 'sales' বা 'invoices' অনুযায়ী)
        $todaySales = 0;
        // DB::table('sales')
        //     ->whereDate('created_at', Carbon::today())
        //     ->sum('total_amount') ?? 0;

        // চলতি মাসের মোট খরচ (কাল্পনিক 'expenses' বা 'cashbook' অনুযায়ী)
        $monthExpenses = 0;
        // DB::table('expenses')
        //     ->whereMonth('created_at', Carbon::now()->month)
        //     ->sum('amount') ?? 0;

        // মোট কাস্টমার ডিউ বা বকেয়া
        $totalDues = 0;
        // DB::table('customers')
        //     ->sum('due_balance') ?? 0;

        // স্টকে থাকা মোট প্রোডাক্টের সংখ্যা
        $totalProducts = 0;
        // DB::table('products')->count();

        // রিসেন্ট ৫টি ইনভয়েস/ট্রানজেকশন শর্ট লিস্ট
        $recentSales = [];
        // DB::table('sales')
        //     ->orderBy('id', 'desc')
        //     ->limit(5)
        //     ->get();

        return view('livewire.tenant.dashboard', compact(
            'todaySales', 
            'monthExpenses', 
            'totalDues', 
            'totalProducts',
            'recentSales'
        ));
    }
}