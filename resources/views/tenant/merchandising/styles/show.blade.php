@extends('layouts.tenant')
@section('title', 'Style Overview & Buyer Cost Sheet')

@section('content')
@php
    $costing = $style->costing;
    $bomItems = $costing ? $costing->bomItems : collect();
    
    // Grouping BOM items
    $fabrics = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') === 'fabrics');
    $trims = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') !== 'fabrics');

    // Totals calculation
    $ttlFabricCost = $fabrics->sum(fn($i) => $i->consumption * $i->unit_price);
    $ttlTrimCost = $trims->sum(fn($i) => $i->consumption * $i->unit_price);
    
    $printCost = floatval($costing->print_cost ?? 0);
    $embCost = floatval($costing->emb_cost ?? 0);
    $washCost = floatval($costing->wash_cost ?? 0);
    $valueAddCost = $printCost + $embCost + $washCost;

    $cmCost = floatval($costing->cm_cost ?? 0);
    $overheadCost = floatval($costing->overhead_cost ?? 0);
    $makingCost = $cmCost + $overheadCost;

    $ttlBaseCost = $ttlFabricCost + $ttlTrimCost + $valueAddCost + $makingCost;

    // Markup calculations
    $revenuePercent = floatval($costing->revenue_percent ?? 6);
    $revenueAmt = $ttlBaseCost * ($revenuePercent / 100);
    $ttlWithRevenue = $ttlBaseCost + $revenueAmt;

    $aitPercent = floatval($costing->ait_percent ?? 5);
    $aitAmt = $ttlWithRevenue * ($aitPercent / 100);
    $ttlWithAit = $ttlWithRevenue + $aitAmt;

    $vatPercent = floatval($costing->vat_percent ?? 10);
    $vatAmt = $ttlWithAit * ($vatPercent / 100);

    $ttlFob = $ttlWithAit + $vatAmt;
    $offeredPrice = floatval($costing->offered_price ?? $ttlFob);
    $targetPrice = $costing->target_fob;

    $currencySymbol = ($costing->currency ?? 'USD') === 'USD' ? '$' : '৳';
@endphp

<div class="max-w-7xl mx-auto p-4 space-y-6">

    <!-- Top Action & Navigation Header -->
    <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Style Master & Buyer Cost Sheet</h2>
            <p class="text-xs text-slate-500">Style Code: <span class="font-mono font-bold text-indigo-600">{{ $style->style_number }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.merch.styles') }}" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">Back</a>
            <a href="{{ route('tenant.merch.styles.edit', $style->id) }}" class="px-4 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Edit Style</a>
            <a href="{{ route('tenant.merch.styles.export-pdf', $style->id) }}" class="px-4 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Export PDF</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COST SHEET TABLE (Matching Excel Format) -->
        <div class="lg:col-span-8 bg-white border border-slate-300 rounded-lg shadow-sm overflow-hidden">
            
            <!-- Table Header Bar -->
            <div class="bg-amber-700 text-dark p-3 flex justify-between items-center text-xs font-bold uppercase tracking-wider">
                <span>{{ $style->product_name }}</span>
                <span>Target Price: {{ $currencySymbol }} {{ number_format($targetPrice ?? $offeredPrice, 2) }}</span>
            </div>
            <div class="bg-amber-600 text-white py-1.5 text-center text-xs font-bold uppercase tracking-widest border-t border-amber-500">
                Buyer Cost Sheet
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 font-bold border-b border-slate-300 text-slate-700">
                            <th class="p-2 border-r border-slate-300 w-3/12">Item</th>
                            <th class="p-2 border-r border-slate-300 w-4/12">Details & UOM</th>
                            <th class="p-2 border-r border-slate-300 text-right w-1.5/12">Qty</th>
                            <th class="p-2 border-r border-slate-300 text-right w-1.5/12">Unit Price</th>
                            <th class="p-2 border-r border-slate-300 text-right w-1.5/12">Wastage (%)</th>
                            <th class="p-2 text-right w-2/12">TTL Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium text-slate-800">

                        <!-- 1. FABRICS SECTION -->
                        @forelse($fabrics as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-semibold text-indigo-900">
                                {{ $item->itemMaster->category->name ?? $item->item_description ?? 'Fabrics' }}
                            </td>
                            <td class="p-2 border-r border-slate-200 text-slate-600">
                                {{ $item->item->details ?? $item->item_description }}
                                @if(optional($item->itemMaster)->unit)
                                    <span class="text-[10px] bg-slate-100 px-1 rounded border text-slate-500 ml-1">({{ $item->itemMaster->unit->short_name }})</span>
                                @endif
                            </td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->consumption, 2) }}</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->wastage_percent, 2) }}%</td>
                            <td class="p-2 text-right font-mono font-semibold">{{ $currencySymbol }} {{ number_format($item->consumption * $item->unit_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-2 border-r border-slate-200 text-slate-400 italic">No fabric items listed.</td>
                        </tr>
                        @endforelse

                        <!-- TOTAL FABRIC COST -->
                        <tr class="bg-amber-100 font-bold border-y-2 border-amber-300 text-slate-900">
                            <td colspan="2" class="p-2 border-r border-slate-300 uppercase">TTL Fabric Cost</td>
                            <td class="p-2 border-r border-slate-300 text-right font-mono">{{ number_format($fabrics->sum('consumption'), 2) }}</td>
                            <td class="p-2 border-r border-slate-300"></td>
                            <td class="p-2 border-r border-slate-300"></td>
                            <td class="p-2 text-right font-mono text-sm text-amber-900">{{ $currencySymbol }} {{ number_format($ttlFabricCost, 2) }}</td>
                        </tr>

                        <!-- 2. TRIMS & ACCESSORIES SECTION -->
                        @foreach($trims as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-semibold uppercase text-slate-700">
                                {{ $item->itemMaster->category->name ?? $item->item_description ?? $item->item->name ?? 'Trim' }}
                            </td>
                            <td class="p-2 border-r border-slate-200 text-slate-600">
                                {{ $item->item->details ?? $item->item_description }}
                                @if(optional($item->itemMaster)->unit)
                                    <span class="text-[10px] bg-slate-100 px-1 rounded border text-slate-500 ml-1">({{ $item->itemMaster->unit->short_name }})</span>
                                @endif
                            </td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->consumption, 2) }}</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->wastage_percent, 2) }}%</td>
                            <td class="p-2 text-right font-mono font-semibold">{{ $currencySymbol }} {{ number_format($item->consumption * $item->unit_price, 2) }}</td>
                        </tr>
                        @endforeach

                        <!-- TOTAL TRIM COST -->
                        <tr class="bg-amber-100 font-bold border-y-2 border-amber-300 text-slate-900">
                            <td colspan="5" class="p-2 border-r border-slate-300 uppercase">TTL Trim Cost</td>
                            <td class="p-2 text-right font-mono text-sm text-amber-900">{{ $currencySymbol }} {{ number_format($ttlTrimCost, 2) }}</td>
                        </tr>

                        <!-- 3. VALUE ADD COST (SERVICES) -->
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">PRINT</td>
                            <td colspan="2" class="p-2 border-r border-slate-200 text-slate-500">-</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">1.00</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($printCost, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($printCost, 2) }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">EMB</td>
                            <td colspan="2" class="p-2 border-r border-slate-200 text-slate-500">-</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">1.00</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($embCost, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($embCost, 2) }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">WASH</td>
                            <td colspan="2" class="p-2 border-r border-slate-200 text-slate-500">-</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">1.00</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($washCost, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($washCost, 2) }}</td>
                        </tr>

                        <!-- VALUE ADD COST TOTAL -->
                        <tr class="bg-amber-100 font-bold border-y border-amber-300 text-slate-900">
                            <td colspan="5" class="p-2 border-r border-slate-300 uppercase">Value Add Cost</td>
                            <td class="p-2 text-right font-mono text-sm text-amber-900">{{ $currencySymbol }} {{ number_format($valueAddCost, 2) }}</td>
                        </tr>

                        <!-- 4. MAKING COST (CM & OVERHEAD) -->
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-bold text-slate-700 uppercase">CM</td>
                            <td colspan="2" class="p-2 border-r border-slate-200 text-slate-500">-</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">1.00</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($cmCost, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($cmCost, 2) }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 border-r border-slate-200 font-bold text-slate-700 uppercase">OVERHEAD COST</td>
                            <td colspan="2" class="p-2 border-r border-slate-200 text-slate-500">-</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">1.00</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($overheadCost, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($overheadCost, 2) }}</td>
                        </tr>

                        <!-- MAKING COST TOTAL -->
                        <tr class="bg-amber-100 font-bold border-y-2 border-amber-300 text-slate-900">
                            <td colspan="4" class="p-2 border-r border-slate-300 uppercase">Making Cost</td>
                            <td class="p-2 border-r border-slate-300 text-right font-mono">1.00</td>
                            <td class="p-2 text-right font-mono text-sm text-amber-900">{{ $currencySymbol }} {{ number_format($makingCost, 2) }}</td>
                        </tr>

                        <!-- 5. SUMMARY GRAND TOTALS & MARKUPS -->
                        <tr class="bg-amber-800 text-white font-bold">
                            <td colspan="4" class="p-2.5 border-r border-amber-700 uppercase tracking-wider">TTL Cost (Base)</td>
                            <td class="p-2.5 border-r border-amber-700 text-right font-mono">1.00</td>
                            <td class="p-2.5 text-right font-mono text-base">{{ $currencySymbol }} {{ number_format($ttlBaseCost, 2) }}</td>
                        </tr>

                        <tr class="bg-sky-100 text-blue-900 font-bold">
                            <td colspan="3" class="p-2 border-r border-sky-200 uppercase">REVENUE</td>
                            <td class="p-2 border-r border-sky-200 text-right font-mono text-rose-600 font-extrabold">{{ number_format($revenuePercent, 2) }}%</td>
                            <td class="p-2 border-r border-sky-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($revenueAmt, 2) }}</td>
                            <td class="p-2 text-right font-mono font-bold">{{ $currencySymbol }} {{ number_format($ttlWithRevenue, 2) }}</td>
                        </tr>

                        <tr class="bg-slate-50 text-slate-800 font-bold">
                            <td colspan="3" class="p-2 border-r border-slate-200 uppercase">AIT- {{ number_format($aitPercent, 0) }}%</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($aitPercent, 2) }}%</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($aitAmt, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlWithAit, 2) }}</td>
                        </tr>

                        <tr class="bg-slate-50 text-slate-800 font-bold">
                            <td colspan="3" class="p-2 border-r border-slate-200 uppercase">VAT {{ number_format($vatPercent, 0) }}%</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($vatPercent, 2) }}%</td>
                            <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($vatAmt, 2) }}</td>
                            <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlFob, 2) }}</td>
                        </tr>

                        <tr class="bg-amber-800 text-white font-bold text-sm">
                            <td colspan="4" class="p-2.5 border-r border-amber-700 uppercase tracking-wider">TTL FOB</td>
                            <td class="p-2.5 border-r border-amber-700 text-right font-mono">1.00</td>
                            <td class="p-2.5 text-right font-mono text-base">{{ $currencySymbol }} {{ number_format($ttlFob, 2) }}</td>
                        </tr>

                        <tr class="bg-emerald-500 text-slate-950 font-black text-sm">
                            <td colspan="4" class="p-3 border-r border-emerald-600 uppercase tracking-widest">OFFERED PRICE</td>
                            <td class="p-3 border-r border-emerald-600 text-right font-mono">1.00</td>
                            <td class="p-3 text-right font-mono text-lg">{{ $currencySymbol }} {{ number_format($offeredPrice, 2) }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT SIDE: Style Overview & Preview Panel -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Style Image Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Style Preview</h4>
                @if($style->product_image)
                    <img src="{{ tenant_asset($style->product_image) }}" alt="{{ $style->product_name }}" class="w-full h-64 object-cover rounded-lg border border-slate-200 shadow-inner">
                @else
                    <div class="w-full h-64 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 text-xs font-semibold">
                        No Image Uploaded
                    </div>
                @endif
            </div>

            <!-- Master Overview Info Card -->
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-2">Master Information</h4>
                
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Buyer</span>
                        <span class="font-semibold text-slate-800">{{ $style->buyer->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Season</span>
                        <span class="font-semibold text-slate-800">{{ $style->season->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Currency</span>
                        <span class="font-semibold text-slate-800">{{ $costing->currency ?? 'USD' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Status</span>
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $style->status ?? 'Active' }}
                        </span>
                    </div>
                </div>

                @if($style->description)
                <div class="pt-2 border-t text-xs">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Remarks / Description</span>
                    <p class="text-slate-600 mt-1 italic">{{ $style->description }}</p>
                </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection