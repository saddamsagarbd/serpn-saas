@extends('layouts.tenant')
@section('title','Cash Register Box')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
        <div>
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Cash Register Box</h4>
            <p class="text-xs text-slate-400 mt-0.5">Physical liquid assets kept across outlets.</p>
        </div>
        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60 text-center space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Vault Liquid Value</span>
            <p class="text-2xl font-black font-mono text-indigo-600">78,500.00 ৳</p>
        </div>
        <div class="text-xs text-slate-500 space-y-2 font-medium pt-1">
            <div class="flex justify-between"><span>Main Safe Pool:</span> <span class="font-mono font-bold text-slate-800">65,000 ৳</span></div>
            <div class="flex justify-between"><span>Counter Terminal-1:</span> <span class="font-mono font-bold text-slate-800">13,500 ৳</span></div>
        </div>
    </div>

    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Cash Ledger Statements</h4>
            <p class="text-xs text-slate-400 mt-0.5">Running ledger statement filtering cash incoming and outgoing registers.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-3">Date</th>
                        <th class="p-3 pb-3">Description Context</th>
                        <th class="p-3 pb-3 text-right">Cash In (+)</th>
                        <th class="p-3 pb-3 text-right">Cash Out (-)</th>
                        <th class="p-3 pr-0 pb-3 text-right">Net Balance</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="py-3.5 pl-0 font-mono text-slate-400">09-07-2026</td>
                        <td class="py-3.5">
                            <p class="font-bold text-slate-800">Initial Cash Balance Opening</p>
                            <span class="text-[9px] text-indigo-500 font-semibold font-mono">SYSTEM-OPEN</span>
                        </td>
                        <td class="py-3.5 text-right font-mono font-semibold text-slate-400">0.00 ৳</td>
                        <td class="py-3.5 text-right font-mono font-semibold text-slate-400">0.00 ৳</td>
                        <td class="py-3.5 pr-0 text-right font-bold font-mono text-slate-900">66,000 ৳</td>
                    </tr>
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="py-3.5 pl-0 font-mono text-slate-400">09-07-2026</td>
                        <td class="py-3.5">
                            <p class="font-bold text-slate-800">POS Retail Cash Sales Payment Collection</p>
                            <span class="text-[9px] text-indigo-500 font-semibold font-mono">INV-2026-0782</span>
                        </td>
                        <td class="py-3.5 text-right font-mono font-bold text-emerald-600">+12,500 ৳</td>
                        <td class="py-3.5 text-right font-mono font-semibold text-slate-400">0.00 ৳</td>
                        <td class="py-3.5 pr-0 text-right font-bold font-mono text-slate-900">78,500 ৳</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection