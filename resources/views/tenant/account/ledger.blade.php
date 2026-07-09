@extends('layouts.tenant')
@section('title','General Ledger Account Book')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">General Ledger Account Book</h4>
            <p class="text-xs text-slate-400 mt-0.5">Filter specific account codes to audit chronological running statements and balances.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-200/60 transition">💾 Export PDF</button>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">🖨️ Print Statement</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50/40 rounded-xl border border-slate-200/40">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Ledger *</label>
            <select class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>1010-001 - Petty Cash Counter</option>
                <option>1020-005 - DBBL Bank Account</option>
                <option>4010-001 - Retail Sales Revenue</option>
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
                <tr class="bg-white border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-0 pb-3">Date</th>
                    <th class="p-3 pb-3">Voucher Ref</th>
                    <th class="p-3 pb-3">Narration Description</th>
                    <th class="p-3 pb-3 text-right">Debit (DR)</th>
                    <th class="p-3 pb-3 text-right">Credit (CR)</th>
                    <th class="p-3 pr-0 pb-3 text-right">Running Balance</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="bg-slate-50/30">
                    <td class="py-3 pl-0 text-slate-400 font-mono">{{ date('01-m-Y') }}</td>
                    <td class="py-3 font-mono text-slate-400">-</td>
                    <td class="py-3 font-bold text-slate-500 italic">Statement Opening Balance Brought Forward</td>
                    <td class="py-3 text-right font-mono text-slate-300">0.00 ৳</td>
                    <td class="py-3 text-right font-mono text-slate-300">0.00 ৳</td>
                    <td class="py-3 pr-0 text-right font-bold font-mono text-slate-800">66,000.00 ৳</td>
                </tr>
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="py-3 pl-0 text-slate-500 font-mono">{{ date('d-m-Y') }}</td>
                    <td class="py-3 font-mono font-bold text-indigo-600">JV-002911</td>
                    <td class="py-3 font-medium text-slate-700">Cash received from POS counter terminal sales batch</td>
                    <td class="py-3 text-right font-mono font-bold text-emerald-600">+12,500.00 ৳</td>
                    <td class="py-3 text-right font-mono text-slate-300">0.00 ৳</td>
                    <td class="py-3 pr-0 text-right font-bold font-mono text-slate-900">78,500.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection