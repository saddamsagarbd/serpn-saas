@extends('layouts.tenant')
@section('title','Item Entry')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'item-entry', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'item-entry'" x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">Create New Master Item</h3>
                <p class="text-xs text-gray-500 mt-1">Register a new product or component into the global inventory database.</p>
            </div>
            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Name *</label>
                        <input type="text" placeholder="e.g., Execution Ergonomic Mesh Chair" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-medium">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">SKU / Item Code *</label>
                        <input type="text" placeholder="e.g., ITM-HW-04918" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono text-slate-800 font-bold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Category</label>
                        <select class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700 font-semibold">
                            <option>Select Category</option>
                            <option>Office Furniture</option>
                            <option>Hardware & Electronics</option>
                            <option>Raw Materials</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Barcode (UPC/EAN)</label>
                        <input type="text" placeholder="Scan or enter barcode" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Unit of Measure (UOM)</label>
                        <select class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700 font-semibold">
                            <option>Pcs (Pieces)</option>
                            <option>Box</option>
                            <option>Kg (Kilogram)</option>
                            <option>Mtr (Meter)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Default Cost Price (৳)</label>
                        <input type="number" step="0.01" placeholder="0.00" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold font-mono text-right">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Default Selling Price (৳)</label>
                        <input type="number" step="0.01" placeholder="0.00" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold font-mono text-right">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Reorder Alert Level</label>
                        <input type="number" placeholder="e.g., 10" class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-mono">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Description / Specifications</label>
                    <textarea rows="3" placeholder="Enter product specifications or batch notes..." class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800"></textarea>
                </div>

                <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">Save Master Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection