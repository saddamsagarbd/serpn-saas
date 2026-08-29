@extends('layouts.tenant')
@section('title', 'Purchase Return Details')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Buttons -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Debit Note / Return Details</h3>
            <p class="text-xs text-slate-500">Viewing details for return reference: <span class="font-mono font-bold text-slate-700">#{{ $return->id }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.purchase.return') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm">
                ← Back to List
            </a>
            <button onclick="window.print()" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                🖨 Print Debit Note
            </button>
        </div>
    </div>

    <!-- Info Summary Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">GRN Reference</span>
            <span class="text-xs font-bold text-slate-800 mt-1 block">
                {{ $return->goodsReceivedNote->grn_no ?? 'N/A' }}
            </span>
        </div>
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Return Date</span>
            <span class="text-xs font-mono font-bold text-slate-800 mt-1 block">
                {{ $return->return_date ?? $return->created_date->format('Y-m-d') }}
            </span>
        </div>
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Warehouse</span>
            <span class="text-xs font-bold text-slate-800 mt-1 block">
                {{ $return->warehouse->name ?? 'N/A' }}
            </span>
        </div>
        <div>
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Reason / Remarks</span>
            <span class="text-xs text-slate-600 mt-1 block italic">
                {{ $return->reason ?? 'No reason provided' }}
            </span>
        </div>
    </div>

    <!-- Return Items Table -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Returned Line Items</h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b">
                        <th class="p-3">#</th>
                        <th class="p-3">Item Description</th>
                        <th class="p-3 text-right">Return Qty</th>
                        <th class="p-3 text-right">Unit Price (৳)</th>
                        <th class="p-3 text-right">Subtotal (৳)</th>
                        <th class="p-3">Remarks</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @foreach($return->items as $index => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-bold text-slate-800">
                                {{ $item->stock->itemVariant->name ?? ($item->itemMaster->name ?? 'Item #' . $item->name) }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-rose-600">
                                {{ number_format($item->return_qty, 2) }}
                            </td>
                            <td class="p-3 text-right font-mono text-slate-600">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-slate-800">
                                {{ number_format($item->total_amount, 2) }} ৳
                            </td>
                            <td class="p-3 text-slate-500 italic">
                                {{ $item->remarks ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Grand Total Summary -->
        <div class="flex justify-end items-center pt-4 border-t border-slate-100">
            <div class="text-right font-mono">
                <span class="text-xs font-bold text-slate-500 uppercase">Grand Total Return Value: </span>
                <span class="text-sm font-bold text-rose-600 pl-2">
                    {{ number_format($return->items->sum('total_amount'), 2) }} ৳
                </span>
            </div>
        </div>
    </div>
</div>
@endsection