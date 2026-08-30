@extends('layouts.tenant')
@section('title', 'Production Bill of Materials (BOM) - ' . $style->style_number)

@section('content')
@php
    $costing = $style->costing;
    $bomItems = $costing ? $costing->bomItems : collect();
    
    // $orderQty কন্ট্রোলার থেকে আসছে (MPR ভিত্তিক)
    $orderQuantity = floatval($orderQty ?? 1); 

    $fabrics = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') === 'fabrics');
    $trims = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') !== 'fabrics');

    $currencySymbol = ($costing->currency ?? 'USD') === 'USD' ? '$' : '৳';

    $calcRowData = function($item) use ($orderQuantity) {
        $cons = floatval($item->consumption ?? 0);
        $excessPercent = floatval($item->wastage_percent ?? 5);
        
        // ১ পিস রেসিপি x MPR Order Qty
        $totalReqQty = ($orderQuantity * $cons) * (1 + ($excessPercent / 100));
        
        // ইন-হাউস এবং প্রাইজ ডাটাবেজ থেকে পাওয়া যাবে
        $inhouseQty = floatval($item->stocks?->sum('available_qty') ?? 0);
        $stockEntryDate = $item->latestStock?->created_at?->format('d-m-Y') ?? '';
        $shortageQty = $inhouseQty - $totalReqQty;
        $unitPrice = floatval($item->unit_price ?? 0);
        $totalBudget = $totalReqQty * $unitPrice;

        return (object)[
            'excessPercent' => $excessPercent,
            'totalReqQty' => $totalReqQty,
            'inhouseQty' => $inhouseQty,
            'shortageQty' => $shortageQty,
            'unitPrice' => $unitPrice,
            'totalBudget' => $totalBudget,
            'grn_date' => $stockEntryDate,
        ];
    };
@endphp

<div class="max-w-full mx-auto p-4 space-y-6">

    <!-- Top Action & Navigation Header -->
    <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-indigo-100 text-indigo-700">Production BOM</span>
                <h2 class="text-lg font-bold text-slate-800">Production Bill of Materials (BOM)</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Style Code: <span class="font-mono font-bold text-indigo-600">{{ $style->style_number }}</span> | 
                Product: <span class="font-bold text-slate-700">{{ $style->product_name }}</span> | 
                Order Qty: <span class="font-mono font-bold text-slate-900">{{ number_format($orderQty) }} Pcs</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.merch.styles') }}" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">Back</a>
            <a href="{{ route('tenant.merch.styles.edit', $style->id) }}" class="px-4 py-1.5 text-xs font-semibold bg-slate-700 hover:bg-slate-800 text-white rounded-lg">Edit Style</a>
            <a href="{{ route('tenant.merch.styles.export-dom-pdf', $style->id) }}" class="px-4 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm">Export PDF</a>
        </div>
    </div>

    <!-- MAIN PRODUCTION BOM TABLE -->
    <div class="bg-white border border-slate-300 rounded-xl shadow-sm overflow-hidden">
        
        <!-- Excel Style Amber Bar Header -->
        <div class="bg-amber-700 text-white p-3 flex justify-between items-center text-xs font-bold uppercase tracking-wider">
            <span>Style: {{ $style->style_number }} ({{ $style->product_name }})</span>
            <span>Buyer: {{ $style->buyer->name ?? 'N/A' }} | Season: {{ $style->season->name ?? 'N/A' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse min-w-[1200px]">
                <thead>
                    <tr class="bg-slate-800 text-white font-bold uppercase tracking-wider text-[10px]">
                        <th class="p-2.5 border-r border-slate-700">Material Description</th>
                        <th class="p-2.5 border-r border-slate-700">GMT Color</th>
                        <th class="p-2.5 border-r border-slate-700">Req. Mat. Color</th>
                        <th class="p-2.5 border-r border-slate-700 text-right">GMT Order Qty</th>
                        <th class="p-2.5 border-r border-slate-700 text-right">Cons / GMT</th>
                        <th class="p-2.5 border-r border-slate-700 text-right">Excess %</th>
                        <th class="p-2.5 border-r border-slate-700 text-right bg-indigo-900">Total Req. Qty</th>
                        <th class="p-2.5 border-r border-slate-700 text-right bg-emerald-900">In-House Qty</th>
                        <th class="p-2.5 border-r border-slate-700 text-right">Short / Excess</th>
                        <th class="p-2.5 border-r border-slate-700 text-center">GRN Date</th>
                        <th class="p-2.5 border-r border-slate-700 text-center">PCD Date</th>
                        <th class="p-2.5 border-r border-slate-700 text-center">Status</th>
                        <th class="p-2.5 border-r border-slate-700 text-right">Unit Price</th>
                        <th class="p-2.5 text-right bg-amber-900">Total Budget</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">

                    <!-- ================= 1. FABRICS SECTION ================= -->
                    <tr class="bg-indigo-50/70 text-indigo-950 font-black uppercase tracking-wider border-y border-indigo-200">
                        <td colspan="13" class="p-2 px-3">1. Fabric Materials</td>
                    </tr>
                    @php $ttlFabricBudget = 0; $ttlFabricReq = 0; @endphp
                    @forelse($fabrics as $item)
                    @php
                        $data = $calcRowData($item);
                        $ttlFabricBudget += $data->totalBudget;
                        $ttlFabricReq += $data->totalReqQty;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="p-2 border-r border-slate-200 font-bold text-slate-900">
                            {{ $item->item_description ?? $item->itemMaster->name ?? 'Fabric' }}
                        </td>
                        <td class="p-2 border-r border-slate-200">{{ $item->gmt_color ?? 'All Color' }}</td>
                        <td class="p-2 border-r border-slate-200 text-slate-600">{{ $item->material_color ?? 'As per GMT' }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($orderQty) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->consumption, 4) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono text-amber-700 font-bold">{{ number_format($data->excessPercent, 2) }}%</td>
                        
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold text-indigo-700 bg-indigo-50/40">
                            {{ number_format($data->totalReqQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold text-emerald-700 bg-emerald-50/40">
                            {{ number_format($data->inhouseQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold {{ $data->shortageQty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($data->shortageQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $data->grn_date ? \Carbon\Carbon::parse($data->grn_date)->format('Y-m-d') : 'TBC' }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                        
                        <td class="p-2 border-r border-slate-200 text-center">
                            @if($data->shortageQty >= 0 && $data->inhouseQty > 0)
                                <span class="px-2 py-0.5 text-[10px] bg-emerald-100 text-emerald-800 rounded font-bold">In-Housed</span>
                            @elseif($data->inhouseQty > 0)
                                <span class="px-2 py-0.5 text-[10px] bg-amber-100 text-amber-800 rounded font-bold">Partial</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] bg-rose-100 text-rose-800 rounded font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }}{{ number_format($data->unitPrice, 2) }}</td>
                        <td class="p-2 text-right font-mono font-bold text-slate-900">{{ $currencySymbol }}{{ number_format($data->totalBudget, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="p-2 text-center text-slate-400 italic">No fabric items listed.</td>
                    </tr>
                    @endforelse

                    <!-- FABRIC SUB-TOTAL -->
                    <tr class="bg-amber-100/60 font-bold border-y border-amber-300 text-slate-900">
                        <td colspan="6" class="p-2 text-right uppercase border-r border-amber-300">Total Fabric Requirement</td>
                        <td class="p-2 text-right font-mono border-r border-amber-300 font-bold text-indigo-900">{{ number_format($ttlFabricReq, 2) }}</td>
                        <td colspan="6" class="border-r border-amber-300"></td>
                        <td class="p-2 text-right font-mono text-sm font-black text-amber-950">{{ $currencySymbol }}{{ number_format($ttlFabricBudget, 2) }}</td>
                    </tr>


                    <!-- ================= 2. TRIMS & ACCESSORIES SECTION ================= -->
                    <tr class="bg-indigo-50/70 text-indigo-950 font-black uppercase tracking-wider border-y border-indigo-200">
                        <td colspan="14" class="p-2 px-3">2. Trims & Accessories</td>
                    </tr>
                    @php $ttlTrimBudget = 0; $ttlTrimReq = 0; @endphp
                    @forelse($trims as $item)
                    @php
                        $data = $calcRowData($item);
                        $ttlTrimBudget += $data->totalBudget;
                        $ttlTrimReq += $data->totalReqQty;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="p-2 border-r border-slate-200 font-bold text-slate-900">
                            {{ $item->item_description ?? $item->itemMaster->name ?? 'Trim Item' }}
                        </td>
                        <td class="p-2 border-r border-slate-200">{{ $item->gmt_color ?? 'All Color' }}</td>
                        <td class="p-2 border-r border-slate-200 text-slate-600">{{ $item->material_color ?? 'DTM' }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($orderQty) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ number_format($item->consumption, 4) }}</td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono text-amber-700 font-bold">{{ number_format($data->excessPercent, 2) }}%</td>
                        
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold text-indigo-700 bg-indigo-50/40">
                            {{ number_format($data->totalReqQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold text-emerald-700 bg-emerald-50/40">
                            {{ number_format($data->inhouseQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono font-bold {{ $data->shortageQty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($data->shortageQty, 2) }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                        
                        <td class="p-2 border-r border-slate-200 text-center">
                            @if($data->shortageQty >= 0 && $data->inhouseQty > 0)
                                <span class="px-2 py-0.5 text-[10px] bg-emerald-100 text-emerald-800 rounded font-bold">In-Housed</span>
                            @elseif($data->inhouseQty > 0)
                                <span class="px-2 py-0.5 text-[10px] bg-amber-100 text-amber-800 rounded font-bold">Partial</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] bg-rose-100 text-rose-800 rounded font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-mono">{{ $currencySymbol }}{{ number_format($data->unitPrice, 2) }}</td>
                        <td class="p-2 text-right font-mono font-bold text-slate-900">{{ $currencySymbol }}{{ number_format($data->totalBudget, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="p-2 text-center text-slate-400 italic">No trim items listed.</td>
                    </tr>
                    @endforelse

                    <!-- TRIMS SUB-TOTAL -->
                    <tr class="bg-amber-100/60 font-bold border-y border-amber-300 text-slate-900">
                        <td colspan="6" class="p-2 text-right uppercase border-r border-amber-300">Total Trim Requirement</td>
                        <td class="p-2 text-right font-mono border-r border-amber-300 font-bold text-indigo-900">{{ number_format($ttlTrimReq, 2) }}</td>
                        <td colspan="6" class="border-r border-amber-300"></td>
                        <td class="p-2 text-right font-mono text-sm font-black text-amber-950">{{ $currencySymbol }}{{ number_format($ttlTrimBudget, 2) }}</td>
                    </tr>

                    <!-- ================= GRAND TOTAL BUDGET ================= -->
                    <tr class="bg-slate-900 text-white font-black text-sm">
                        <td colspan="13" class="p-3 text-right uppercase tracking-wider border-r border-slate-800">
                            Grand Total Material Budget (Fabric + Trims)
                        </td>
                        <td class="p-3 text-right font-mono text-amber-400 text-base">
                            {{ $currencySymbol }}{{ number_format($ttlFabricBudget + $ttlTrimBudget, 2) }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection