@extends('layouts.tenant')
@section('title','Sales Performance Report')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Comprehensive Sales Performance Report</h4>
            <p class="text-xs text-slate-400 mt-0.5">Track point-of-sale invoices, gross turnover, items sold configurations, and tax data logs.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-200/60 transition">💾 Export Excel</button>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">🖨️ Print Report</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-slate-50/40 rounded-xl border border-slate-200/40">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customer Group</label>
            <select class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>All Customers</option>
                <option>Walk-In Retail Counter</option>
                <option>Corporate Accounts</option>
            </select>
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment Mode</label>
            <select class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>All Methods</option>
                <option>Cash Vault</option>
                <option>Bank Transfer / Card</option>
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
                    <th class="p-3 pl-4">Invoice #</th>
                    <th class="p-3">Order Date</th>
                    <th class="p-3">Customer Entity</th>
                    <th class="p-3 text-center">Payment Status</th>
                    <th class="p-3 text-right">Subtotal</th>
                    <th class="p-3 text-right">Discount</th>
                    <th class="p-3 pr-4 text-right">Grand Net Paid</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-3 pl-4 font-mono font-bold text-indigo-600">INV-2026-0089</td>
                    <td class="p-3 font-mono text-slate-500">09-07-2026</td>
                    <td class="p-3 font-bold text-slate-800">Walk-In Retail Counter Customer</td>
                    <td class="p-3 text-center"><span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 text-[9px] font-bold rounded">Cleared</span></td>
                    <td class="p-3 text-right font-mono text-slate-600">12,500.00 ৳</td>
                    <td class="p-3 text-right font-mono text-rose-500">500.00 ৳</td>
                    <td class="p-3 pr-4 text-right font-bold font-mono text-slate-900">12,000.00 ৳</td>
                </tr>
                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3 pl-4 uppercase tracking-wider text-[10px]" colspan="4">Report Dynamic Total Turnover</td>
                    <td class="p-3 text-right font-mono text-slate-700">12,500.00 ৳</td>
                    <td class="p-3 text-right font-mono text-rose-600">500.00 ৳</td>
                    <td class="p-3 pr-4 text-right font-mono text-indigo-600">12,000.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection