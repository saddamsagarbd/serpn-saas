@extends('layouts.tenant')
@section('title','Goods Receivable Notes (GRN)')
@section('content')

<div class="space-y-6" x-data='grnComponent(@json($purchaseOrders))'>
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

            <!-- 🛠️ PO Selection Section -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Purchase Order (PO)</label>
                    <select x-model="selectedPOId" @change="onPOChange()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        <option value="">-- Choose Pending PO --</option>
                        <template x-for="po in rawPurchaseOrders" :key="po.id">
                            <option :value="po.id" x-text="po.po_no + ' (' + (po.supplier ? po.supplier.name : 'No Supplier') + ')'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <button type="button" @click="checkPO()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Fetch PO Details
                    </button>
                </div>
            </div>

            <!-- 📄 ERP STANDARD GRN GENERATION DETAILS -->
            <div x-show="showDetails" x-transition class="space-y-6 border-t border-gray-200 pt-6" x-cloak>
                <form action="{{ route('tenant.purchase.grn.store') }}" method="POST" class="p-6 space-y-5 text-xs">
                    @csrf
                    
                    <!-- Hidden PO ID Field -->
                    <input type="hidden" name="purchase_order_id" :value="selectedPOId">

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
                            <input type="text" name="grn_no" value="GRN-{{ date('Ymd') }}-{{ strtoupper(Str::random(4)) }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-xs font-semibold text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Challan / Delivery Note No.*</label>
                            <input type="text" name="challan_no" required placeholder="e.g. CH-99823" class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Receiving Warehouse</label>
                            <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Date Received</label>
                            <input type="date" name="received_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Itemized Verification Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-800 text-white flex justify-between items-center">
                            <h3 class="text-xs font-bold uppercase tracking-wider">Item Verification & QA Check</h3>
                            <span class="text-xs text-gray-300" x-text="'Linked PO No: ' + (activePO ? activePO.po_no : '')"></span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Item Description</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Color / Size</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-blue-50 text-blue-700">Ordered</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-orange-50 text-orange-700">Prev Received</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase bg-green-50 text-green-700 w-32">Current Receive</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-36">Remarks / QA Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="(item, index) in poItems" :key="index">
                                        <tr :class="item.is_completed ? 'bg-gray-50 text-gray-400' : ''">
                                            <!-- Hidden Inputs for Form Submission -->
                                            <input type="hidden" :name="`items[${index}][po_item_id]`" :value="item.po_item_id">
                                            <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id">

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900" x-text="item.name"></div>
                                                <div class="text-xs text-gray-400 font-mono" x-text="'Code: ' + (item.item_code || 'N/A')"></div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-xs font-semibold text-gray-600">
                                                <span x-text="item.color"></span> / <span x-text="item.size"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold bg-blue-50/50" x-text="item.ordered + ' ' + item.unit"></td>
                                            <td class="px-4 py-3 text-center font-medium bg-orange-50/50" x-text="item.prev_received + ' ' + item.unit"></td>
                                            <td class="px-4 py-2 bg-green-50/30">
                                                <input type="number" 
                                                       step="0.01" 
                                                       :name="`items[${index}][receiving_qty]`" 
                                                       x-model.number="item.receiving" 
                                                       :max="item.ordered - item.prev_received" 
                                                       :disabled="item.is_completed" 
                                                       class="block w-full text-center rounded border-gray-300 text-xs font-bold shadow-sm focus:border-green-500 focus:ring-green-500 py-1" 
                                                       :class="item.is_completed ? 'bg-gray-100' : ''">
                                            </td>
                                            <td class="px-4 py-2">
                                                <select :name="`items[${index}][qa_status]`" x-model="item.status" :disabled="item.is_completed" class="block w-full rounded border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                                    <option value="Good">Passed (Good)</option>
                                                    <option value="Damaged">Damaged / Rejected</option>
                                                    <option value="Partial">Partial Approved</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="poItems.length === 0">
                                        <td colspan="6" class="p-4 text-center text-gray-400 italic">No items found in this purchase order.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Footer Actions -->
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="text-xs text-gray-500">
                            <span class="font-bold text-red-500">* Note:</span> Submitting this GRN will automatically increase stock levels in the selected warehouse and sync Accounts Payable ledger.
                        </div>
                        <div class="flex space-x-2 gap-2">
                            <button type="button" @click="resetForm()" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-lg text-xs text-white bg-emerald-600 hover:bg-emerald-700 font-semibold shadow-xs">
                                Verify & Post GRN Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function grnComponent(rawPOs) {
        return {
            currentTab: 'purchase-order',
            rawPurchaseOrders: rawPOs || [],
            selectedPOId: '',
            activePO: null,
            showDetails: false,
            poItems: [],

            onPOChange() {
                this.showDetails = false;
            },

            checkPO() {
                if (!this.selectedPOId) {
                    alert('Please select a valid Purchase Order first.');
                    this.showDetails = false;
                    return;
                }

                this.activePO = this.rawPurchaseOrders.find(po => String(po.id) === String(this.selectedPOId));

                if (this.activePO && this.activePO.order) {
                    this.poItems = this.activePO.order.map(orderItem => {
                        let orderedQty = Number(orderItem.order_qty || 0);
                        let prevReceived = Number(orderItem.received_qty || 0); // Database-এ আগের রিসিভড ডাটা থাকলে
                        let remaining = Math.max(0, orderedQty - prevReceived);

                        return {
                            po_item_id: orderItem.id,
                            item_id: orderItem.item_id,
                            item_code: orderItem.item ? (orderItem.item.code || orderItem.item.item_code) : 'N/A',
                            name: orderItem.item ? orderItem.item.name : (orderItem.item_name || 'Item'),
                            color: orderItem.color ? orderItem.color.name : (orderItem.color || '-'),
                            size: orderItem.size ? orderItem.size.name : (orderItem.size || '-'),
                            unit: orderItem.unit ? orderItem.unit.name : (orderItem.unit || 'Pcs'),
                            ordered: orderedQty,
                            prev_received: prevReceived,
                            receiving: remaining,
                            status: remaining === 0 ? 'Completed' : 'Good',
                            is_completed: remaining === 0
                        };
                    });
                    this.showDetails = true;
                } else {
                    this.poItems = [];
                    this.showDetails = false;
                    alert('No items found in selected Purchase Order.');
                }
            },

            resetForm() {
                this.showDetails = false;
                this.selectedPOId = '';
                this.poItems = [];
                this.activePO = null;
            }
        }
    }
</script>
@endsection