@extends('layouts.tenant')
@section('title','Master Audit Trail & Transactions')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Master Audit Trail & Transactions</h4>
            <p class="text-xs text-slate-400 mt-0.5">Comprehensive historic ledger of all double-entry transaction rows logged inside the ecosystem.</p>
        </div>
    </div>

    <div class="px-6 py-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" placeholder="Search by Voucher Code, Memo or Account ID..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-700">
        <select class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-600 font-semibold">
            <option>All Accounts Ledger</option>
            <option>1010-001 - Petty Cash Counter</option>
            <option>1020-005 - DBBL Bank Account</option>
        </select>
        <input type="date" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6">Txn Code</th>
                    <th class="p-4">Execution Date</th>
                    <th class="p-4">Target Ledger Group</th>
                    <th class="p-4">Reference Narration Memo</th>
                    <th class="p-4 text-right">Debit Side</th>
                    <th class="p-4 text-right">Credit Side</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-4 font-bold font-mono text-indigo-600">TXN-009821</td>
                    <td class="p-4 font-mono text-slate-500">09-07-2026</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Petty Cash Operational Counter</p>
                        <span class="text-[9px] font-mono bg-slate-100 px-1 py-0.5 text-slate-500 rounded font-bold">1010-001</span>
                    </td>
                    <td class="p-4 text-slate-600 font-medium">POS Order Ticket Counter Invoice #2026-A Cash Collection</td>
                    <td class="p-4 text-right font-bold font-mono text-slate-900">12,500 ৳</td>
                    <td class="p-4 text-right font-bold font-mono text-slate-300">0.00 ৳</td>
                </tr>
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-4 font-bold font-mono text-indigo-600">TXN-009822</td>
                    <td class="p-4 font-mono text-slate-500">09-07-2026</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Retail ERP POS Sales Income</p>
                        <span class="text-[9px] font-mono bg-indigo-50 px-1 py-0.5 text-indigo-500 rounded font-bold">4010-001</span>
                    </td>
                    <td class="p-4 text-slate-400 italic">Double-entry balancing leg for Counter Invoice #2026-A</td>
                    <td class="p-4 text-right font-bold font-mono text-slate-300">0.00 ৳</td>
                    <td class="p-4 text-right font-bold font-mono text-emerald-600">12,500 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection