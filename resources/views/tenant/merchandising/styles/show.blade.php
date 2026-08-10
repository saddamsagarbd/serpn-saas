@extends('layouts.tenant')
@section('content')
<div class="max-w-5xl mx-auto space-y-6 my-6">
    <!-- Header Summary Block -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $style->style_number }} : {{ $style->product_name }}</h2>
            <p class="text-xs text-slate-500">Buyer: <b>{{ $style->buyer->name }}</b> | Season: <b>{{ $style->season->name }}</b></p>
        </div>
        
        <!-- Action Control Center -->
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.merch.styles.edit', $style->id) }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                Modify Style
            </a>
            <!-- PDF Export Button -->
            <a href="{{ route('tenant.merch.styles.export-pdf', $style->id) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- BOM Line Items List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6">Material Description</th>
                    <th class="p-4">Category</th>
                    <th class="p-4 text-right">Consumption</th>
                    <th class="p-4 text-right">Unit Price</th>
                    <th class="p-4 text-right pr-6">Total Cost</th>
                </tr>
            </thead>
            <tbody class="text-xs text-slate-700 divide-y divide-slate-100">
                @foreach($style->costing->bomItems as $item)
                    <tr>
                        <td class="p-4 pl-6 font-semibold">{{ $item->item_description }}</td>
                        <td class="p-4 uppercase text-[10px] font-bold text-slate-500">{{ $item->category }}</td>
                        <td class="p-4 text-right font-mono">{{ number_format($item->consumption, 4) }} {{ $item->unit }}</td>
                        <td class="p-4 text-right font-mono">${{ number_format($item->unit_price, 4) }}</td>
                        <td class="p-4 text-right font-mono font-bold pr-6">${{ number_format($item->total_cost, 4) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection