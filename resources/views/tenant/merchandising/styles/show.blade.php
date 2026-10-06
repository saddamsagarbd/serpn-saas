@extends('layouts.tenant')
@section('title', 'Style Overview & Buyer Cost Sheet')

@section('content')
@php
    $costing = $style->costing;
    $bomItems = $costing ? $costing->bomItems : collect();
    
    // Grouping BOM items
    $fabrics = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') === 'fabrics');
    $trims = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') !== 'fabrics');

    // Effective Item Cost Helper (Including Wastage %)
    $getItemCost = function($item) {
        $consumption = floatval($item->consumption ?? 0);
        $unitPrice = floatval($item->unit_price ?? 0);
        $wastage = floatval($item->wastage_percent ?? 0);
        
        $effectiveQty = $consumption + ($consumption * ($wastage / 100));
        return $effectiveQty * $unitPrice;
    };

    // Totals calculation
    $ttlFabricCost = $fabrics->sum(fn($i) => $getItemCost($i));
    $ttlTrimCost = $trims->sum(fn($i) => $getItemCost($i));
    
    $printCost = floatval($costing->print_cost ?? 0);
    $printCostWastage = floatval($costing->print_cost ?? 0);
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
    $targetPrice = floatval($costing->target_fob ?? 0);

    $currencySymbol = ($costing->currency ?? 'USD') === 'USD' ? '$' : '৳';
@endphp

<div class="p-3 sm:p-6 space-y-6 max-w-[1600px] mx-auto">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Style Master & Buyer Cost Sheet</h2>
            <p class="text-xs text-slate-500">Style Code: <span class="font-mono font-bold text-indigo-600">{{ $style->style_number }}</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('tenant.merch.styles') }}" class="flex-1 sm:flex-none text-center px-3 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">Back</a>
            <a href="{{ route('tenant.merch.styles.edit', $style->id) }}" class="flex-1 sm:flex-none text-center px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Edit Style</a>
            <a href="{{ route('tenant.merch.styles.export-pdf', $style->id) }}" target="_blank" class="flex-1 sm:flex-none text-center px-4 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">Export PDF</a>
        </div>
    </div>

    <!-- TOP SECTION: STYLE OVERVIEW & IMAGE PREVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm items-center">
        
        <!-- Style Image Block -->
        <div class="md:col-span-3 lg:col-span-2 flex justify-center">
            @if($style->product_image)
                <img src="{{ tenant_asset($style->product_image) }}" alt="{{ $style->product_name }}" class="w-full max-w-[140px] h-36 object-cover rounded-lg border border-slate-200 shadow-sm">
            @else
                <div class="w-full max-w-[140px] h-36 bg-slate-50 rounded-lg flex flex-col items-center justify-center text-slate-400 text-xs font-semibold gap-1 border border-dashed border-slate-300">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>No Preview</span>
                </div>
            @endif
        </div>

        <!-- Master Overview Key Information -->
        <div class="md:col-span-9 lg:col-span-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-6">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Product Name</span>
                <span class="font-bold text-slate-800 text-sm block truncate">{{ $style->product_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Buyer Name</span>
                <span class="font-semibold text-slate-800 block truncate">{{ $style->buyer->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Season</span>
                <span class="font-semibold text-slate-800 block truncate">{{ $style->season->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Currency</span>
                <span class="font-semibold text-slate-800">{{ $costing->currency ?? 'USD' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Target FOB</span>
                <span class="font-bold font-mono text-slate-800">{{ $currencySymbol }} {{ number_format($targetPrice, 2) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Offered FOB</span>
                <span class="font-black font-mono text-emerald-600 text-sm">{{ $currencySymbol }} {{ number_format($offeredPrice, 2) }}</span>
            </div>

            @if($targetPrice > 0)
            <div class="col-span-2 sm:col-span-3 lg:col-span-6 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-slate-500 font-medium">Margin Variance (Offered vs Target):</span>
                @php $diff = $offeredPrice - $targetPrice; @endphp
                @if($diff <= 0)
                    <span class="font-bold text-emerald-600 font-mono bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $currencySymbol }} {{ number_format(abs($diff), 2) }} Under Target</span>
                @else
                    <span class="font-bold text-rose-600 font-mono bg-rose-50 px-2 py-0.5 rounded border border-rose-200">{{ $currencySymbol }} {{ number_format($diff, 2) }} Over Target</span>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- MAIN COST SHEET TABLE (FULL WIDTH & FULLY RESPONSIVE) -->
    <div class="bg-white border border-slate-300 rounded-lg shadow-sm overflow-hidden">
        
        <!-- Table Header Bar -->
        <div class="bg-amber-800 text-white p-3 flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs font-bold uppercase tracking-wider gap-2">
            <span>{{ $style->product_name }} — Itemized Breakdown</span>
            <span class="bg-amber-900/80 px-2.5 py-1 rounded border border-amber-700">Target FOB: {{ $currencySymbol }} {{ number_format($targetPrice > 0 ? $targetPrice : $offeredPrice, 2) }}</span>
        </div>
        <div class="bg-amber-600 text-white py-1.5 text-center text-xs font-bold uppercase tracking-widest border-t border-amber-500">
            Buyer Cost Sheet Summary
        </div>

        <!-- Table Container with Horizontal Scroll fallback for very small screens -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                <thead>
                    <tr class="bg-slate-100 font-bold border-b border-slate-300 text-slate-700">
                        <th class="p-2.5 border-r border-slate-300 w-3/12">Item</th>
                        <th class="p-2.5 border-r border-slate-300 w-3/12">Details</th>
                        <th class="p-2.5 border-r border-slate-300 text-center w-1.5/12">Qty</th>
                        <th class="p-2.5 border-r border-slate-300 text-right w-1.5/12">Unit Price</th>
                        <th class="p-2.5 border-r border-slate-300 text-center w-1/12">UOM</th>
                        <th class="p-2.5 border-r border-slate-300 text-center w-1/12">Wastage</th>
                        <th class="p-2.5 text-right w-2/12">TTL Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">

                    <!-- 1. FABRICS SECTION -->
                    @forelse($fabrics as $item)
                    @php $itemCost = $getItemCost($item); @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-2 border-r border-slate-200 font-semibold text-indigo-900">
                            {{ $item->itemMaster->category->name ?? $item->item_description ?? 'Fabrics' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-slate-600">
                            {{ $item->item->details ?? $item->item_description ?? 'N/A' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ number_format($item->consumption, 3) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 3) }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $item->item_unit ?? $item->itemMaster->unit->short_name ?? 'YDS' }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ number_format($item->wastage_percent, 1) }}%</td>
                        <td class="p-2 text-right font-mono font-semibold">{{ $currencySymbol }} {{ number_format($itemCost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-3 border-r border-slate-200 text-center text-slate-400 italic bg-slate-50">No fabric items listed in cost sheet.</td>
                    </tr>
                    @endforelse

                    <!-- TOTAL FABRIC COST -->
                    <tr class="bg-amber-100/80 font-bold border-y-2 border-amber-300 text-slate-900">
                        <td colspan="2" class="p-2 border-r border-slate-300 uppercase">TTL Fabric Cost</td>
                        <td class="p-2 border-r border-slate-300 text-center font-mono">{{ number_format($fabrics->sum('consumption'), 3) }}</td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 text-right font-mono text-sm text-amber-950">{{ $currencySymbol }} {{ number_format($ttlFabricCost, 2) }}</td>
                    </tr>

                    <!-- 2. TRIMS & ACCESSORIES SECTION -->
                    @forelse($trims as $item)
                    @php $itemCost = $getItemCost($item); @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-2 border-r border-slate-200 font-semibold uppercase text-slate-700">
                            {{ $item->itemMaster->category->name ?? $item->item_description ?? $item->item->name ?? 'Trim' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-slate-600">
                            {{ $item->item->details ?? $item->item_description ?? 'N/A' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ number_format($item->consumption, 3) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 3) }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $item->item_unit ?? $item->itemMaster->unit->short_name ?? 'DZN' }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ number_format($item->wastage_percent, 1) }}%</td>
                        <td class="p-2 text-right font-mono font-semibold">{{ $currencySymbol }} {{ number_format($itemCost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-3 border-r border-slate-200 text-center text-slate-400 italic bg-slate-50">No trim items listed.</td>
                    </tr>
                    @endforelse

                    <!-- TOTAL TRIM COST -->
                    <tr class="bg-amber-100/80 font-bold border-y-2 border-amber-300 text-slate-900">
                        <td colspan="2" class="p-2 border-r border-slate-300 uppercase">TTL Trim Cost</td>
                        <td class="p-2 border-r border-slate-300 text-center font-mono">{{ number_format($trims->sum('consumption'), 3) }}</td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 text-right font-mono text-sm text-amber-950">{{ $currencySymbol }} {{ number_format($ttlTrimCost, 2) }}</td>
                    </tr>

                    <!-- 3. VALUE ADD COST (SERVICES) -->
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">PRINT</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($printCost, 2) }}</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">PCS</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($printCost, 2) }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">EMB</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($embCost, 2) }}</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">PCS</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($embCost, 2) }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 font-bold text-indigo-700 uppercase">WASH</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($washCost, 2) }}</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">PCS</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($washCost, 2) }}</td>
                    </tr>

                    <!-- VALUE ADD COST TOTAL -->
                    <tr class="bg-amber-100/80 font-bold border-y border-amber-300 text-slate-900">
                        <td colspan="6" class="p-2 border-r border-slate-300 uppercase">Value Add Cost Total</td>
                        <td class="p-2 text-right font-mono text-sm text-amber-950">{{ $currencySymbol }} {{ number_format($valueAddCost, 2) }}</td>
                    </tr>

                    <!-- 4. MAKING COST (CM & OVERHEAD) -->
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 font-bold text-slate-700 uppercase">CM</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($cmCost, 2) }}</td>                            
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">DZN</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($cmCost, 2) }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 font-bold text-slate-700 uppercase">OVERHEAD COST</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($overheadCost, 2) }}</td>                            
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">DZN</td>
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($overheadCost, 2) }}</td>
                    </tr>

                    <!-- MAKING COST TOTAL -->
                    <tr class="bg-amber-100/80 font-bold border-y-2 border-amber-300 text-slate-900">
                        <td colspan="2" class="p-2 border-r border-slate-300 uppercase">Making Cost Total</td>
                        <td class="p-2 border-r border-slate-300 text-center font-mono">1.00</td>
                        <td class="p-2 text-right font-mono border-r border-slate-300">{{ $currencySymbol }} {{ number_format($makingCost, 2) }}</td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 border-r border-slate-300"></td>
                        <td class="p-2 text-right font-mono text-sm text-amber-950">{{ $currencySymbol }} {{ number_format($makingCost, 2) }}</td>
                    </tr>

                    <!-- 5. SUMMARY GRAND TOTALS & MARKUPS -->
                    <tr class="bg-slate-800 text-white font-bold">
                        <td colspan="2" class="p-2.5 border-r border-slate-700 uppercase tracking-wider">TTL Cost (Base)</td>
                        <td class="p-2.5 border-r border-slate-700 text-center font-mono">1.00</td>
                        <td class="p-2 border-r border-slate-700 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-700 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-700 text-slate-400">-</td>
                        <td class="p-2.5 text-right font-mono text-base">{{ $currencySymbol }} {{ number_format($ttlBaseCost, 2) }}</td>
                    </tr>

                    <tr class="bg-sky-50 text-blue-900 font-bold">
                        <td colspan="2" class="p-2 border-r border-sky-200 uppercase">REVENUE</td>
                        <td class="p-2 border-r border-sky-200 text-right font-mono text-indigo-600 font-extrabold">{{ number_format($revenuePercent, 2) }}%</td>
                        <td class="p-2 border-r border-sky-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($revenueAmt, 2) }}</td>
                        <td class="p-2 border-r border-sky-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-sky-200 text-slate-400">-</td>
                        <td class="p-2 text-right font-mono font-bold">{{ $currencySymbol }} {{ number_format($ttlWithRevenue, 2) }}</td>
                    </tr>

                    <tr class="bg-slate-50 text-slate-800 font-bold">
                        <td colspan="2" class="p-2 border-r border-slate-200 uppercase">AIT ({{ number_format($aitPercent, 0) }}%)</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($aitPercent, 2) }}%</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($aitAmt, 2) }}</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlWithAit, 2) }}</td>
                    </tr>

                    <tr class="bg-slate-50 text-slate-800 font-bold">
                        <td colspan="2" class="p-2 border-r border-slate-200 uppercase">VAT ({{ number_format($vatPercent, 0) }}%)</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($vatPercent, 2) }}%</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }} {{ number_format($vatAmt, 2) }}</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 border-r border-slate-200 text-slate-400">-</td>
                        <td class="p-2 text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlFob, 2) }}</td>
                    </tr>

                    <tr class="bg-amber-800 text-white font-bold text-sm">
                        <td colspan="6" class="p-2.5 border-r border-amber-700 uppercase tracking-wider">TTL FOB</td>
                        <td class="p-2.5 text-right font-mono text-base">{{ $currencySymbol }} {{ number_format($ttlFob, 2) }}</td>
                    </tr>

                    <tr class="bg-emerald-500 text-slate-950 font-black text-sm">
                        <td colspan="6" class="p-3 border-r border-emerald-600 uppercase tracking-widest">OFFERED PRICE</td>
                        <td class="p-3 text-right font-mono text-lg">{{ $currencySymbol }} {{ number_format($offeredPrice, 2) }}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection