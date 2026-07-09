@extends('layouts.tenant')
@section('title','Customer CRM Directory')
@section('content')
<div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
    <!-- হেডার -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Customer CRM Directory</h4>
            <p class="text-xs text-slate-400 mt-0.5">Manage business accounts, retail buyers, profiles, and historical balance statement mappings.</p>
        </div>
        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition">
            + Register New Profile
        </button>
    </div>

    <!-- ফিল্টারিং সেকশন -->
    <div class="px-6 py-2 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-72">
            <input type="text" placeholder="Search by Client Name or Email..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
        </div>
    </div>

    <!-- কাস্টমার ডিরেক্টরি টেবিল -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6">Client Code</th>
                    <th class="p-4">Customer Info</th>
                    <th class="p-4">Contact / Email</th>
                    <th class="p-4 text-right">Lifetime Total Bought</th>
                    <th class="p-4 text-right">Receivable Due Balance</th>
                    <th class="p-4 pr-6 text-center">Record Row Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-bold font-mono text-slate-900">CST-00491</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Anisur Rahman</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-600 mt-0.5">VIP Retail Tier</span>
                    </td>
                    <td class="p-4 font-mono text-slate-600">
                        <p>anis@domain-example.com</p>
                        <p class="text-slate-400 text-[10px]">+880 1712-000000</p>
                    </td>
                    <td class="p-4 text-right font-bold font-mono text-slate-800">432,000 ৳</td>
                    <td class="p-4 text-right font-bold font-mono text-rose-600">0.00 ৳</td>
                    <td class="p-4 pr-6 text-center">
                        <button type="button" class="text-xs font-bold text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-slate-200/60 transition">🛠️ Edit Statement</button>
                    </td>
                </tr>
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-bold font-mono text-slate-900">CST-00492</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Beximco Office Corp</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-600 mt-0.5">Wholesale Account</span>
                    </td>
                    <td class="p-4 font-mono text-slate-600">
                        <p>procurement@beximco.corp</p>
                        <p class="text-slate-400 text-[10px]">+880 2-984000</p>
                    </td>
                    <td class="p-4 text-right font-bold font-mono text-slate-800">2,450,000 ৳</td>
                    <td class="p-4 text-right font-bold font-mono text-rose-600">45,000 ৳</td>
                    <td class="p-4 pr-6 text-center">
                        <button type="button" class="text-xs font-bold text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-slate-200/60 transition">🛠️ Edit Statement</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection