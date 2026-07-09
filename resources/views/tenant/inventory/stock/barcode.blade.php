@extends('layouts.tenant')
@section('title','Barcode')
@section('content')
<div x-data="{ 
    selectedItems: [], 
    selectAll: false,
    toggleAll() {
        this.selectAll = !this.selectAll;
        this.selectedItems = this.selectAll ? [1, 2, 3] : [];
    }
}" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- হেডার অ্যাকশন টপবার -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Barcode Label Generator</h4>
            <p class="text-xs text-slate-400 mt-0.5">Encode item SKUs into standard CODE128 alphanumeric symbology for physical printing.</p>
        </div>
        
        <div>
            <button type="button" 
                    :disabled="selectedItems.length === 0"
                    :class="selectedItems.length > 0 ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl transition-all">
                📦 Bulk Generate Barcodes <span x-show="selectedItems.length > 0" x-text="'(' + selectedItems.length + ')'" class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]"></span>
            </button>
        </div>
    </div>

    <!-- ফিল্টার কন্ট্রোল -->
    <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-72">
            <input type="text" placeholder="Search by SKU or Item Name..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <select class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-600 font-semibold">
                <option>All Categories</option>
                <option>Office Furniture</option>
                <option>Hardware & Electronics</option>
            </select>
        </div>
    </div>

    <!-- প্রোডাক্ট বারকোড রেন্ডার তালিকা -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6 w-12 text-center">
                        <input type="checkbox" @click="toggleAll()" x-model="selectAll" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="p-4 w-2/12">SKU / Item Code</th>
                    <th class="p-4 w-4/12">Item Details</th>
                    <th class="p-4 w-4/12 text-center">Generated Barcode (CODE128)</th>
                    <th class="p-4 pr-6 w-2/12 text-right">Row Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                
                <!-- রো ১: অলরেডি জেনারেটেড -->
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 pl-6 text-center">
                        <input type="checkbox" value="1" x-model="selectedItems" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="p-4 font-black font-mono text-slate-900 tracking-wide">ITM-HW-04918</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Execution Ergonomic Mesh Chair</p>
                        <p class="text-[10px] text-slate-400">Category: Office Furniture • Qty: 45 Pcs</p>
                    </td>
                    <td class="p-4 text-center">
                        <div class="inline-flex flex-col items-center bg-white p-2 rounded-lg border border-slate-100 shadow-xs">
                            <div class="flex items-center h-8 bg-slate-900 px-8 py-1 tracking-[3px] font-mono text-[9px] text-white overflow-hidden rounded-xs opacity-95 relative" style="letter-spacing: 2px;">
                                ||||| | |||| || | |||| ||
                            </div>
                            <span class="text-[9px] font-bold font-mono tracking-widest text-slate-600 mt-1">ITM-HW-04918</span>
                        </div>
                    </td>
                    <td class="p-4 pr-6 text-right">
                        <button type="button" class="text-xs font-bold text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-slate-200/60 transition">
                            🖨️ Print Label
                        </button>
                    </td>
                </tr>

                <!-- রো ২: নিউ বা নট জেনারেটেড স্টেট -->
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 pl-6 text-center">
                        <input type="checkbox" value="2" x-model="selectedItems" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="p-4 font-black font-mono text-slate-900 tracking-wide">ITM-EL-11029</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Premium USB-C Multi-Port Hub</p>
                        <p class="text-[10px] text-slate-400">Category: Hardware & Electronics • Qty: 120 Pcs</p>
                    </td>
                    <td class="p-4 text-center">
                        <span class="text-[11px] font-medium text-slate-400 italic">No barcode generated yet</span>
                    </td>
                    <td class="p-4 pr-6 text-right">
                        <button type="button" class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg shadow-xs transition">
                            Generate
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- পেজিনেশন ফুটার -->
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/20">
        <span class="text-xs text-slate-400 font-medium">Showing 1 to 2 of 2 items</span>
        <div class="flex items-center gap-2">
            <button type="button" class="px-3 py-1 text-xs font-bold text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed" disabled>Previous</button>
            <button type="button" class="px-3 py-1 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Next</button>
        </div>
    </div>
</div>
@endsection