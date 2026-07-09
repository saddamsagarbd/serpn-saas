@extends('layouts.tenant')
@section('title','Stock Adjustment')
@section('content')
<div x-data="{ entryMode: 'single' }" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- টপবার এবং মোড টগল বোতাম -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Inventory Stock Inward / Adjustment</h4>
            <p class="text-xs text-slate-400 mt-0.5">Receive or adjust physical inventory inside tenant warehouses.</p>
        </div>
        
        <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/60 self-start sm:self-auto">
            <button type="button" @click="entryMode = 'single'" :class="entryMode === 'single' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">
                Single Item
            </button>
            <button type="button" @click="entryMode = 'bulk'" :class="entryMode === 'bulk' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">
                Bulk Entry
            </button>
        </div>
    </div>

    <form class="p-6 space-y-5">
        <!-- ইনফরমেশন মেটা ব্লক -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Entry Date</label>
                <input type="date" value="2026-07-09" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700 font-semibold">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Reference Voucher</label>
                <input type="text" value="STK-20260709-001" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-400 font-bold font-mono">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Source / Reason</label>
                <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-750 font-semibold">
                    <option>Purchase Inward</option>
                    <option>Stock Audit / Adjustment</option>
                    <option>Inter-Warehouse Transfer</option>
                </select>
            </div>
        </div>

        <!-- সিগেল আইটেম প্যানেল -->
        <div x-show="entryMode === 'single'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="md:col-span-2 space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Select Item *</label>
                <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
                    <option>Search and Choose Product...</option>
                    <option>ITM-HW-04918 - Execution Ergonomic Mesh Chair</option>
                    <option>ITM-EL-11029 - Premium USB-C Multi-Port Hub</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Warehouse *</label>
                <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
                    <option>Central Warehousing Unit-2</option>
                    <option>Dhaka Banani Outlet Depot</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Quantity *</label>
                <input type="number" placeholder="0" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-900 font-bold text-right">
            </div>
        </div>

        <!-- বাল্ক আইটেম গ্রিড টেবিল -->
        <div x-show="entryMode === 'bulk'" x-transition class="space-y-3">
            <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-5/12">Master Item</th>
                            <th class="p-3 w-4/12">Warehouse Destination</th>
                            <th class="p-3 w-2/12 text-right">Qty to Add</th>
                            <th class="p-3 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="p-2.5 pl-4">
                                <select class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                    <option>ITM-HW-04918 - Execution Ergonomic Mesh Chair</option>
                                </select>
                            </td>
                            <td class="p-2.5">
                                <select class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                    <option>Central Warehousing Unit-2</option>
                                </select>
                            </td>
                            <td class="p-2.5">
                                <input type="number" value="12" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800">
                            </td>
                            <td class="p-2.5 text-center">
                                <button type="button" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg transition">✕</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <button type="button" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50/50 px-3 py-2 rounded-xl transition border border-dashed border-indigo-200">
                + Add Item Line
            </button>
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Internal Remarks / Memo</label>
            <input type="text" placeholder="Optional notes regarding this batch..." class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
        </div>

        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                Post Stock Ledger
            </button>
        </div>
    </form>
</div>
@endsection