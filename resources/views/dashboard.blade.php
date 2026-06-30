@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ currentTab: 'dashboard', openModal: false }">
    <!-- হেডিং এবং অ্যাকশন বাটন -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Welcome to SERPN SaaS Engine</h2>
        <button class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-xs hover:bg-blue-700 shadow-sm transition">
            📥 Download Report
        </button>
    </div>
    
    <!-- ট্যাবগুলোর কন্টেন্ট র্যাপার -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        
        <!-- DASHBOARD TAB CONTENT -->
        <div x-show="currentTab === 'dashboard'" x-transition class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">SaaS Overview Analytics</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Active Tenants</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $tenants->count() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600 text-xl">🏢</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Monthly Recurring Revenue</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">৳ ১,৮৫,০০০</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg text-green-600 text-xl">💵</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Premium Subs</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">১২টি</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg text-amber-600 text-xl">⭐</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System Health</p>
                        <p class="text-3xl font-extrabold text-green-600 mt-1">99.9%</p>
                    </div>
                    <div class="p-3 bg-teal-50 rounded-lg text-teal-600 text-xl">⚡</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-700 mb-4">System Activity logs</h3>
                <div class="text-sm text-gray-500 space-y-3">
                    <p class="flex items-center justify-between p-2.5 bg-gray-50 rounded"><span>🟢 Database <code>tenant_rahim</code> provisioned successfully.</span> <span class="text-xs">2 mins ago</span></p>
                    <p class="flex items-center justify-between p-2.5 bg-gray-50 rounded"><span>🟢 Subdomain <code>rahim.serpn-saas.test</code> routing active.</span> <span class="text-xs">5 mins ago</span></p>
                </div>
            </div>
        </div>

        <!-- PLANS TAB CONTENT -->
        <div x-show="currentTab === 'plans'" x-transition class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Subscription & Price Packaging</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Basic Startup</h3>
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded">Active</span>
                    </div>
                    <p class="text-3xl font-extrabold text-indigo-600">৳ ১,৫০০ <span class="text-xs font-normal text-gray-400">/ Monthly</span></p>
                    <ul class="text-xs text-gray-600 space-y-2 border-t pt-3">
                        <li>✔️ Single Warehouse Stock Tracking</li>
                        <li>✔️ Up to 500 Invoices / Month</li>
                        <li>❌ Website E-commerce Integration</li>
                    </ul>
                </div>

                <div class="bg-white border-2 border-indigo-600 p-6 rounded-xl shadow-md space-y-4 relative">
                    <div class="absolute top-0 right-6 -translate-y-1/2 bg-indigo-600 text-white text-[10px] font-bold px-3 py-0.5 rounded-full uppercase">Most Popular</div>
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Enterprise POS</h3>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded">Active</span>
                    </div>
                    <p class="text-3xl font-extrabold text-indigo-600">৳ ৩,৫০০ <span class="text-xs font-normal text-gray-400">/ Monthly</span></p>
                    <ul class="text-xs text-gray-600 space-y-2 border-t pt-3">
                        <li>✔️ Unlimited Raw Materials & Ready Products</li>
                        <li>✔️ Barcode Scanning Support</li>
                        <li>✔️ Automated Integrated Website</li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection