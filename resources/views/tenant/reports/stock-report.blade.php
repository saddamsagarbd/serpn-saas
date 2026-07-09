@extends('layouts.tenant')
@section('title','Inventory Stock Level & Valuation Report')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Live Inventory Stock Level & Valuation Report</h4>
            <p class="text-xs text-slate-400 mt-0.5">Monitor quantities available on hand matched against asset purchasing cost valuations.</p>
        </div>
        <button class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-200/60 transition">💾 Export CSV</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-4">Item SKU</th>
                    <th class="p-3">Product Title Specifications</th>
                    <th class="p-3 text-center">Stock Unit Weight</th>
                    <th class="p-3 text-right">Avg Unit Sourcing Cost</th>
                    <th class="p-3 text-right">Stock Level Qty</th>
                    <th class="p-3 pr-4 text-right">Total Dynamic Asset Valuation</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50/40 transition">
                    <td class="p-3 pl-4 font-mono font-bold text-indigo-600">ITM-HW-04918</td>
                    <td class="p-3 font-bold text-slate-800">Execution Ergonomic Mesh Chair</td>
                    <td class="p-3 text-center"><span class="bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-500 rounded">Furniture</span></td>
                    <td class="p-3 text-right font-mono text-slate-600">8,500.00 ৳</td>
                    <td class="p-3 text-right font-mono font-bold text-slate-800">45 Pcs</td>
                    <td class="p-3 pr-4 text-right font-black font-mono text-slate-900">382,500.00 ৳</td>
                </tr>
                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3.5 pl-4 uppercase tracking-wider text-[10px]" colspan="4">Total Cumulative Asset Holding Valuation</td>
                    <td class="p-3.5 text-right font-mono text-slate-700">45 Pcs</td>
                    <td class="p-3.5 pr-4 text-right font-mono text-indigo-600">382,500.00 ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection