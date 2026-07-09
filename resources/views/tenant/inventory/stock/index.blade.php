@extends('layouts.tenant')
@section('title','Stock Ledger')
@section('content')
<div class="space-y-6">
    <!-- ব্যাচ প্রোডাকশন মাস্টার প্যানেল -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        <div>
            <h4 class="text-base font-bold text-slate-900">Batch Production & Stock Entry</h4>
            <p class="text-xs text-slate-400 mt-0.5">Select a master item, destination warehouse, and assign a production batch with auto-incremental barcodes.</p>
        </div>

        <div class="space-y-4">
            <!-- মেটা ফিল্ডস রো -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Production Date</label>
                    <input type="date" value="2026-07-09" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl font-mono text-slate-700 font-semibold focus:outline-none focus:border-indigo-500">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Auto Batch Number</label>
                    <input type="text" value="BAT-20260709-01" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-400 font-bold font-mono">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Production Type / Source</label>
                    <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-semibold">
                        <option>In-House Manufacturing</option>
                        <option>Bulk Import Purchase</option>
                    </select>
                </div>
            </div>

            <!-- কোর সিলেকশন ফিল্ডস রো -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Select Master Item *</label>
                    <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
                        <option>Choose Product...</option>
                        <option selected>ITM-HW-04918 - Ergonomic Mesh Chair</option>
                        <option>ITM-EL-11029 - Premium USB-C Hub</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Target Warehouse *</label>
                    <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
                        <option>Select Destination...</option>
                        <option selected>Central Warehousing Unit-2</option>
                        <option>Dhaka Banani Outlet Depot</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Production Qty *</label>
                    <input type="number" value="600" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono font-bold text-slate-900 text-right">
                </div>

                <div class="flex items-end">
                    <button type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                        <span>⚙️</span> Generate Barcode
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ব্যাচ ট্র্যাকিং লেজার টেবিল -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Date & Batch-wise Stock Ledger</h5>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-6">Entry Date</th>
                        <th class="p-3">Batch Number</th>
                        <th class="p-3">Item Details</th>
                        <th class="p-3 text-right">Total Qty</th>
                        <th class="p-3 text-center">Barcode Range</th>
                        <th class="p-3 pr-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="p-3 pl-6 font-mono text-slate-500">09-07-2026</td>
                        <td class="p-3 font-bold text-indigo-600 font-mono">BAT-20260709-01</td>
                        <td class="p-3">
                            <p class="font-bold text-slate-800">Ergonomic Mesh Chair</p>
                            <p class="text-[10px] text-slate-400">SKU: ITM-HW-04918</p>
                        </td>
                        <td class="p-3 text-right font-bold font-mono text-slate-900">600 Pcs</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-mono text-slate-600 font-bold">
                                #001 ── #600
                            </span>
                        </td>
                        <td class="p-3 pr-6 text-center">
                            <button type="button" class="text-xs font-bold text-indigo-600 hover:underline">Print 600 Labels</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection