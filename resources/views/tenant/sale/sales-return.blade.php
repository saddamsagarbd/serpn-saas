@extends('layouts.tenant')
@section('title','Sales Return')
@section('content')
<div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Sales Return / Credit Notes</h4>
            <p class="text-xs text-slate-400 mt-0.5">Reverse stock entries, record items returned by customers, and track related refunds.</p>
        </div>
        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition">
            + New Return Authorization
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6">Return Reference</th>
                    <th class="p-4">Original Invoice</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Returned Items Summary</th>
                    <th class="p-4 text-right">Refund Amount</th>
                    <th class="p-4 pr-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-bold font-mono text-rose-600">RTN-2026-004</td>
                    <td class="p-4 font-mono font-medium text-slate-500">INV-2026-0782</td>
                    <td class="p-4 font-bold text-slate-800">Anisur Rahman</td>
                    <td class="p-4">
                        <p class="font-medium text-slate-700">Premium USB-C Multi-Port Hub</p>
                        <p class="text-[10px] text-slate-400">Qty: 1 Pcs (Defective Port)</p>
                    </td>
                    <td class="p-4 text-right font-bold font-mono text-slate-900">4,500 ৳</td>
                    <td class="p-4 pr-6 text-right">
                        <button type="button" class="text-xs font-bold text-slate-500 hover:text-indigo-600 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-200/60 transition">View Details</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection