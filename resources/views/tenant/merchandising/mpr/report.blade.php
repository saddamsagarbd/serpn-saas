@extends('layouts.tenant')
@section('title', 'MPR Order Details')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '' }">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        
        <!-- Top Header & Search Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">MPR Order</h2>
                <p class="text-xs text-slate-400 mt-0.5">Calculated requirements grouped by SKU matrix line items.</p>
            </div>

            <!-- Client-side Quick Search -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('tenant.merch.mpr-order-export-pdf', $salesOrder->id) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Search SKU, style, color..." class="border border-gray-300 rounded-lg text-xs px-3 py-2 pl-8 focus:outline-none focus:border-indigo-500 w-64 shadow-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            
            </div>
        </div>

        <!-- 1. Order Header Information -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">1. Order Header Information</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Style</span>
                    <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $salesOrder->style->name ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Buyer Name (Sold-to Party)</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->buyer->name ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Buyer PO Number</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->buyer_po_number ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Ship To Party</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->ship_to_party ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Sales Org</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->sales_org ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Distribution Channel</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->distribution_channel ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Job Mode</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->job_mode ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Division / Merchant Team</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->merchant_team ?? 'N/A'}}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">PO Received Date</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->po_received_date ? \Carbon\Carbon::parse($salesOrder->po_received_date)->format('m / d / Y') : 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Requested Delivery Date</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->requested_delivery_date ? \Carbon\Carbon::parse($salesOrder->requested_delivery_date)->format('m / d / Y') : 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Advance Receive Date</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->advance_receive_date ? \Carbon\Carbon::parse($salesOrder->advance_receive_date)->format('m / d / Y') : 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Plant (Factory)</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->plant ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Shipping Point</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->shipping_point ?? 'N/A' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span class="text-slate-400 block font-medium uppercase text-[10px]">Currency</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block">{{ $salesOrder->currency ?? 'USD ($)' }}</span>
                </div>
            </div>
        </div>

        <!-- SKU Group Cards -->
        <div class="bg-white rounded-xl p-5 border border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h5 class="font-bold text-slate-800">MPR Order</h5>
                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-bold">
                    Total Order Qty: {{ $consolidatedMrpDetails['total_order_qty'] }} Pcs
                </span>
            </div>

            <!-- <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500">
                        <th class="p-3">Category</th>
                        <th class="p-3">Item Details</th>
                        <th class="p-3">Color Context</th>
                        <th class="p-3">Size Chart</th>
                        <th class="p-3 text-right">Consumption</th>
                        <th class="p-3 text-right">Unit</th>
                        <th class="p-3 text-right">Total Required Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($consolidatedMrpDetails['bom_items'] as $item)
                        <tr>
                            <td class="p-3 font-bold text-slate-800">{{ $item['category'] }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $item['item_name'] }}</td>
                            <td class="p-3">{{ $item['color_name'] }}</td>
                            <td class="p-3">{{ $item['size_name'] }}</td>
                            <td class="p-3 text-right font-mono">{{ number_format($item['consumption'], 4) }}</td>
                            <td class="p-3 text-right font-mono">{{ $item['unit'] }}</td>
                            <td class="p-3 text-right font-mono font-bold text-indigo-600">
                                {{ number_format($item['required_qty'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> -->
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500">
                        <th class="p-3">SKU</th>
                        <th class="p-3">Color</th>
                        <th class="p-3">Size</th>
                        <th class="p-3">Ordered Qty</th>
                        <th class="p-3">Unit Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($salesOrder->items as $item)
                        <tr>
                            <td class="p-3 font-bold text-slate-800">{{ $item->sku ?? "N/A" }}</td>
                            <td class="p-3">{{ $item->colorContext->name }}</td>
                            <td class="p-3">{{ $item->sizeChart->name }}</td>
                            <td class="p-3 text-right font-mono">{{ $item->quantity }}</td>
                            <td class="p-3 text-right font-mono">{{ number_format($item->unit_price, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection