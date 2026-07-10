@extends('layouts.tenant')
@section('title','Goods Receivable Notes (GRN)')
@section('content')
<div class="space-y-6" x-data="{ 
    currentTab: 'purchase-order', 
    openModal: false,
    selectedPO: '',
    showDetails: false,
    
    // ডামি ডেটা (ERP স্ট্যান্ডার্ড অনুযায়ী)
    poItems: [
        { item_code: 'ITM-001', name: 'Premium Office Chair', ordered: 50, prev_received: 30, receiving: 20, unit: 'Pcs', status: 'Good' },
        { item_code: 'ITM-002', name: 'Ergonomic Standing Desk', ordered: 10, prev_received: 5, receiving: 5, unit: 'Pcs', status: 'Good' },
        { item_code: 'ITM-003', name: 'USB-C Docking Station', ordered: 100, prev_received: 100, receiving: 0, unit: 'Pcs', status: 'Completed' }
    ],
    
    checkPO() {
        if(this.selectedPO) {
            this.showDetails = true;
        } else {
            this.showDetails = false;
            alert('Please select a valid Purchase Order first.');
        }
    }
}">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <!-- TENANTS TAB CONTENT -->
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            
            <!-- Header -->
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Goods Receivable Notes (GRN)</h2>
                    <p class="text-xs text-gray-500 mt-1">Receive inventory against generated Purchase Orders.</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold bg-green-50 text-green-700 rounded-full border border-green-200">
                    ERP Gate Entry Module
                </span>
            </div>

            <!-- 🛠️ PO Selection Section (ERP Standard) -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Purchase Order (PO)</label>
                    <select x-model="selectedPO" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        <option value="">-- Choose Pending PO --</option>
                        <option value="PO-2026-001">PO-2026-001 (Acme Corp - Pending)</option>
                        <option value="PO-2026-002">PO-2026-002 (Global Traders - Partially Received)</option>
                        <option value="PO-2026-003">PO-2026-003 (Tech Solutions - Pending)</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 items-end">
                    <button type="button" @click="checkPO()" class="w-full inline-flex justify-center items-center px-4 py-2 mt-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Fetch PO Details
                    </button>
                </div>
            </div>

            <!-- 📄 ERP STANDARD GRN GENERATION DETAILS -->
            <div x-show="showDetails" x-transition class="space-y-6 border-t border-gray-200 pt-6">
                <form action="{{ route('tenant.purchase.grn.store') }}" method="POST" class="p-6 space-y-5 text-xs overflow-y-auto">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex gap-3 items-start animate-shake">
                            <span class="text-base mt-0.5">⚠️</span>
                            <div>
                                <p class="font-bold mb-1 text-sm">Action Required:</p>
                                <ul class="list-disc pl-4 space-y-0.5 font-medium text-rose-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    <!-- GRN Meta Form -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">GRN Number</label>
                            <input type="text" value="GRN-{{ date('Ymd') }}-{{ strtoupper(Str::random(4)) }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-xs font-semibold text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Challan / Delivery Note No.*</label>
                            <input type="text" required placeholder="e.g. CH-99823" class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Receiving Warehouse</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Central Warehouse (Floor 1)</option>
                                <option>Raw Material Store</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Date Received</label>
                            <input type="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Itemized Verification Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-800 text-white flex justify-between items-center">
                            <h3 class="text-xs font-bold uppercase tracking-wider">Item Verification & QA Check</h3>
                            <span class="text-xs text-gray-300" x-text="'Linked PO: ' + selectedPO"></span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Item Code</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Item Description</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-blue-50 text-blue-700">Ordered</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-orange-50 text-orange-700">Prev Received</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-green-50 text-green-700 w-32">Current Receive</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-36">Remarks / QA Status</th>
                                    </tr>
                                </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="(item, index) in poItems" :key="index">
                                        <tr :class="item.status === 'Completed' ? 'bg-gray-50 text-gray-400' : ''">
                                            <td class="px-4 py-3 font-mono text-xs font-semibold" x-text="item.item_code"></td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900" x-text="item.name"></div>
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold bg-blue-50/50" x-text="item.ordered + ' ' + item.unit"></td>
                                            <td class="px-4 py-3 text-center font-medium bg-orange-50/50" x-text="item.prev_received + ' ' + item.unit"></td>
                                            <td class="px-4 py-2 bg-green-50/30">
                                                <input type="number" x-model.number="item.receiving" :disabled="item.status === 'Completed'" class="block w-full text-center rounded border-gray-300 text-xs font-bold shadow-sm focus:border-green-500 focus:ring-green-500 py-1" :class="item.status === 'Completed' ? 'bg-gray-100' : ''">
                                            </td>
                                            <td class="px-4 py-2">
                                                <select x-model="item.status" :disabled="item.status === 'Completed'" class="block w-full rounded border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                                    <option value="Good">Passed (Good)</option>
                                                    <option value="Damaged">Damaged / Rejected</option>
                                                    <option value="Completed">Fully Received</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Footer Actions -->
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="text-xs text-gray-500">
                            <span class="font-bold text-red-500">* Note:</span> Submitting this GRN will automatically increase stock levels in the selected warehouse.
                        </div>
                        <div class="flex space-x-2 gap-2">
                            <button type="button" @click="showDetails = false; selectedPO = ''" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" class="px-4 py-2 border border-transparent rounded-lg text-xs text-white bg-emerald-600 hover:bg-emerald-700 font-semibold">
                                Verify & Post GRN Stock
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            
        </div>
    </div>
</div>
@endsection