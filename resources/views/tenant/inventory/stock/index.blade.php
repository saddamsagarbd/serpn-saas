@extends('layouts.tenant')
@section('title','Stock Ledger')
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 mb-4 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        <div>
            <h4 class="text-base font-bold text-slate-900">Batch Production & Stock Entry</h4>
            <p class="text-xs text-slate-400 mt-0.5">Select a master item, destination warehouse, and assign a production batch with auto-incremental barcodes.</p>
        </div>

        <form method="POST" action="{{ route('tenant.inventory.batch.store') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Production Date</label>
                    <input type="date" name="production_date" value="{{ date('Y-2026-m-d') }}" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl font-mono text-slate-700 font-semibold focus:outline-none focus:border-indigo-500">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select Master Item</label>
                    <select name="item_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-700 font-semibold focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Finished Good/Fabric --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} [SKU: {{ $item->sku }}] - Style: {{ $item->style_no ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Production Quantity</label>
                    <input type="number" name="quantity" placeholder="e.g. 600" min="1" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl font-mono text-slate-700 font-semibold focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs transition">
                    ⚡ Register Batch & Post Stock
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Historical Batches & Valuation Summary</h4>
            <p class="text-xs text-slate-400 mt-0.5">Audit log of system-generated production runs linked across Chart of Accounts Asset Nodes.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-6">Style No</th>
                        <th class="p-3">Fabric Code / Brand</th>
                        <th class="p-3">Item Description</th>
                        <th class="p-3 text-center">Color</th>
                        <th class="p-3 text-right">Stock Qty</th>
                        <th class="p-3 pr-6 text-right">Asset Value</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="p-3 pl-6 font-mono font-bold text-indigo-600">{{ $stock->style_no ?? 'N/A' }}</td>
                            <td class="p-3 font-mono text-slate-500">{{ $stock->fabric_code ?? $stock->brand ?? 'Plain' }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $stock->name }}</td>
                            <td class="p-3 text-center"><span class="bg-slate-100 px-2 py-0.5 text-[9px] font-bold rounded text-slate-600 uppercase">{{ $stock->color ?? 'Multi' }}</span></td>
                            <td class="p-3 text-right font-mono font-bold text-slate-900">{{ number_format($stock->stock_qty) }}</td>
                            <td class="p-3 pr-6 text-right font-mono font-bold text-indigo-600">{{ number_format($stock->stock_qty * $stock->purchase_price, 2) }} ৳</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 font-medium">
                                No active manufacturing production batches found in database logs.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection