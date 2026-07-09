@extends('layouts.tenant')
@section('title','Customer Purchase Ledger & Due Aging Report')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Customer Purchase Ledger & Due Aging Report</h4>
            <p class="text-xs text-slate-400 mt-0.5">Track cumulative client orders volume, net total paid, and currently outstanding receivables balances.</p>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition">🖨️ Print Due List</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-4">Client ID</th>
                    <th class="p-3">Customer Profile Name</th>
                    <th class="p-3">Contact Mobile</th>
                    <th class="p-3 text-right">Total Invoiced Billing</th>
                    <th class="p-3 text-right">Total Paid Counter</th>
                    <th class="p-3 pr-4 text-right">Outstanding Balance (Due)</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-3 pl-4 font-mono font-bold text-indigo-600">CST-99102</td>
                    <td class="p-3">
                        <p class="font-bold text-slate-800">Rahman Logistics & Freight Ltd</p>
                        <span class="text-[9px] font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-bold">Corporate</span>
                    </td>
                    <td class="p-3 font-mono text-slate-500">+880 1712-XXXXXX</td>
                    <td class="p-3 text-right font-mono text-slate-600">350,000.00 ৳</td>
                    <td class="p-3 text-right font-mono text-emerald-600">200,000.00 ৳</td>
                    <td class="p-3 pr-4 text-right font-black font-mono text-rose-600">150,000.00 ৳</td>
                </tr>
                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3.5 pl-4 uppercase tracking-wider text-[10px]" colspan="3">Dynamic Portfolio Aggregations</td>
                    <td class="p-3.5 text-right font-mono text-slate-600">350,000.00 ৳</td>
                    <td class="p-3.5 text-right font-mono text-emerald-600">200,000.00 ৳</td>
                    <td class="p-3.5 pr-4 text-right font-mono text-rose-600">150,000.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection