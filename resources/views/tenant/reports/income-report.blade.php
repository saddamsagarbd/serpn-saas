@extends('layouts.tenant')
@section('title','Revenue & Income Sources')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Revenue & Income Sources Breakdown</h4>
            <p class="text-xs text-slate-400 mt-0.5">Analyze incoming cash flows categorized by invoice retail revenue, services, and scrap sales.</p>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition">💾 Export PDF</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50/40 rounded-xl border border-slate-200/40">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Income Head Ledger</label>
            <select class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>All Income Categories</option>
                <option>4010-001 - Retail Sales Revenue</option>
                <option>4020-002 - Scrap & Waste Materials Sales</option>
            </select>
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">From Date</label>
            <input type="date" value="{{ date('Y-m-01') }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">To Date</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-4">Voucher Ref</th>
                    <th class="p-3">Receipt Date</th>
                    <th class="p-3">Source Ledger Account</th>
                    <th class="p-3">Narration Description</th>
                    <th class="p-3 pr-4 text-right">Received Amount</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-3 pl-4 font-mono font-bold text-indigo-600">INC-2026-0419</td>
                    <td class="p-3 font-mono text-slate-500">09-07-2026</td>
                    <td class="p-3">
                        <p class="font-bold text-slate-800">Scrap & Waste Materials Sales</p>
                        <span class="text-[9px] font-mono bg-indigo-50 text-indigo-500 px-1 py-0.5 rounded font-bold">4020-002</span>
                    </td>
                    <td class="p-3 text-slate-600">Old factory wooden pallets sold out in bulk cash counter</td>
                    <td class="p-3 pr-4 text-right font-black font-mono text-slate-900">18,500.00 ৳</td>
                </tr>
                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3.5 pl-4 uppercase tracking-wider text-[10px]" colspan="4">Total Cumulative Income Receipts</td>
                    <td class="p-3.5 pr-4 text-right font-mono text-indigo-600">18,500.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection