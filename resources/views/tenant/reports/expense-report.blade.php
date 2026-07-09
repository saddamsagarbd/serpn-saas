@extends('layouts.tenant')
@section('title','Expense Audit Report')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Operational & Utilities Expense Audit Report</h4>
            <p class="text-xs text-slate-400 mt-0.5">Granular audit track of outflows spent on company maintenance, office utility, and payroll.</p>
        </div>
        <button class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-200/60 transition">💾 Export Excel</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50/40 rounded-xl border border-slate-200/40">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expense Classification Head</label>
            <select class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>All Expense Categories</option>
                <option>5010-001 - Office Showroom Rent</option>
                <option>5010-002 - Electricity & Power Utility</option>
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
                    <th class="p-3">Payment Date</th>
                    <th class="p-3">Expense Ledger Category</th>
                    <th class="p-3">Payment Memo Context</th>
                    <th class="p-3 pr-4 text-right">Amount Disbursed</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-3 pl-4 font-mono font-bold text-indigo-600">EXP-2026-1102</td>
                    <td class="p-3 font-mono text-slate-500">08-07-2026</td>
                    <td class="p-3">
                        <p class="font-bold text-slate-800">Electricity & Power Utility</p>
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-500 px-1 py-0.5 rounded font-bold">5010-002</span>
                    </td>
                    <td class="p-3 text-slate-600">Commercial showroom June billing payment cleared via DBBL Bank Check</td>
                    <td class="p-3 pr-4 text-right font-black font-mono text-rose-600">4,500.00 ৳</td>
                </tr>
                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3.5 pl-4 uppercase tracking-wider text-[10px]" colspan="4">Total Cumulative Outflow Expenses</td>
                    <td class="p-3.5 pr-4 text-right font-mono text-rose-600">4,500.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection