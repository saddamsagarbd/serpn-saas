@extends('layouts.tenant')
@section('title', 'MPR Order Details')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '' }">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        
        <!-- Top Header & Search Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">MPR Order Material Breakdown</h2>
                <p class="text-xs text-slate-400 mt-0.5">Calculated BOM requirements grouped by SKU matrix line items.</p>
            </div>

            <!-- Client-side Quick Search -->
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="Search SKU, style, color..." class="border border-gray-300 rounded-lg text-xs px-3 py-2 pl-8 focus:outline-none focus:border-indigo-500 w-64 shadow-sm">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- SKU Group Cards -->
        <div class="space-y-5">
            @forelse($groupedMrpDetails as $group)
                <div x-show="!searchQuery || '{{ strtolower($group['sku'] . ' ' . $group['style_code'] . ' ' . $group['color_name'] . ' ' . $group['size_name']) }}'.includes(searchQuery.toLowerCase())" 
                     class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden transition hover:border-slate-300">
                    
                    <!-- SKU Header Bar -->
                    <div class="px-5 py-3 bg-slate-50/80 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-lg font-mono font-bold text-xs">
                                SKU: {{ $group['sku'] }}
                            </span>
                            <span class="text-xs text-slate-600 font-medium">
                                Style: <strong class="text-slate-900 font-bold">{{ $group['style_code'] }}</strong>
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-xs">
                            <span class="text-slate-500">Color: <strong class="text-slate-800 uppercase font-semibold">{{ $group['color_name'] }}</strong></span>
                            <span class="text-slate-300">|</span>
                            <span class="text-slate-500">Size: <strong class="text-slate-800 uppercase font-semibold">{{ $group['size_name'] }}</strong></span>
                            <span class="text-slate-300">|</span>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold font-mono">
                                Order Qty: {{ number_format($group['order_qty']) }} Pcs
                            </span>
                        </div>
                    </div>

                    <!-- Component Items Table for this SKU -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-200/60 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="p-3 pl-5">Component Item</th>
                                    <th class="p-3">Color Context</th>
                                    <th class="p-3">Size Chart</th>
                                    <th class="p-3 text-right">Consumption</th>
                                    <th class="p-3 text-right font-bold text-indigo-600 pr-5">Required Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse($group['bom_items'] as $bom)
                                    <tr class="hover:bg-slate-50/40">
                                        <td class="p-3 pl-5 font-semibold text-slate-800">{{ $bom['item_name'] }}</td>
                                        <td class="p-3 text-slate-500 uppercase">{{ $bom['color_name'] }}</td>
                                        <td class="p-3 text-slate-500 uppercase">{{ $bom['size_name'] }}</td>
                                        <td class="p-3 text-right font-mono text-slate-600">{{ number_format($bom['consumption'], 4) }}</td>
                                        <td class="p-3 text-right pr-5 font-mono font-bold text-indigo-600 bg-indigo-50/20">
                                            {{ number_format($bom['required_qty'], 4) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-slate-400 italic">No BOM components configured for this style.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            @empty
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    No SKU line items or BOM requirements found for this order.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection