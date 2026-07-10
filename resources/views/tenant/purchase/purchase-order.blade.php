@extends('layouts.tenant')
@section('title','Purchase Order')
@section('content')
<div class="space-y-6" x-data="{ 
    currentTab: 'purchase-order', 
    openModal: false,
    showPreviewModal: false, 
    activePO: null 
}">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <!-- TENANTS TAB CONTENT -->
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Purchase Order List</h2>
                <a href="{{ route('tenant.purchase.form') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add New Purchase Order
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" placeholder="Search tenants..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">PO Code</th>
                            <th class="p-4">Order Date</th>
                            <th class="p-4">Supplier</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Approval Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600">PO-20260707001</td>
                            <td class="p-4">08-07-2026</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Mr. XXX</td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">10000.00 BDT</span></td>
                            <td class="p-4"><span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-bold">Pending</span></td>
                            <td class="p-4 text-center">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">Approve</button>
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">View</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600">PO-20260707002</td>
                            <td class="p-4">08-07-2026</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Mr. XXX</td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">10000.00 BDT</span></td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">Approve</span></td>
                            <td class="p-4 text-center">
                                <button @click="activePO = 'PO-20260707001'; showPreviewModal = true" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">
                                    <span class="flex items-center gap-3">
                                        <i data-lucide="printer" class="w-4 h-4 text-slate-400"></i>
                                        <span>Print</span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600">PO-20260707003</td>
                            <td class="p-4">08-07-2026</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Mr. XXX</td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">10000.00 BDT</span></td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">Approve</span></td>
                            <td class="p-4 text-center">
                                <button @click="activePO = 'PO-20260707001'; showPreviewModal = true" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">
                                    <span class="flex items-center gap-3">
                                        <i data-lucide="printer" class="w-4 h-4 text-slate-400"></i>
                                        <span>Print</span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600">PO-20260707004</td>
                            <td class="p-4">08-07-2026</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Mr. XXX</td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">10000.00 BDT</span></td>
                            <td class="p-4"><span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-bold">Pending</span></td>
                            <td class="p-4 text-center">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">Approve</button>
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">View</button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('slices.po-print-preview')
</div>
@endsection