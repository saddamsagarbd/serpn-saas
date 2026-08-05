@extends('layouts.tenant')
@section('title','Sales Order Entry')

@section('content')
<div x-data="salesOrderApp({{ json_encode($styles) }})" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- টপবার -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">New Sales Order Entry</h4>
            <p class="text-xs text-slate-400 mt-0.5">Create a buyer sales order linked with style matrix, plants, and commercial channels.</p>
        </div>
    </div>

    <form @submit.prevent="submitOrder" class="p-6 space-y-6">
        @csrf

        <!-- ১. সেলস অর্ডার হেডার সেকশন (Enterprise Header Fields) -->
        <div class="space-y-4">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">1. Order Header Information</h5>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
                <!-- Select Master Style -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select Style *</label>
                    <select x-model="selectedStyleId" @change="onStyleChange" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                        <option value="">-- Choose Master Style --</option>
                        <template x-for="style in availableStyles" :key="style.id">
                            <option :value="style.id" x-text="style.style_number + ' - ' + (style.product_name || style.name || '')"></option>
                        </template>
                    </select>
                </div>

                <!-- Buyer Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Buyer Name (Sold-to Party)</label>
                    <input type="text" :value="buyerName" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-medium cursor-not-allowed" placeholder="Auto-populated">
                </div>

                <!-- Buyer PO Number -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Buyer PO Number *</label>
                    <input type="text" x-model="buyerPoNumber" placeholder="e.g. PO-HM-99821" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                </div>

                <!-- Ship To Party -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ship to Party *</label>
                    <input type="text" x-model="shipToParty" placeholder="Destination / Warehouse Location" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                </div>

                <!-- Sales Org -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sales Org</label>
                    <input type="text" x-model="salesOrg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Distribution Channel -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Distribution Channel *</label>
                    <select x-model="distributionChannel" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                        <option value="Export">Export</option>
                        <option value="Domestic">Domestic</option>
                    </select>
                </div>

                <!-- Job Mode -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Job Mode *</label>
                    <select x-model="jobMode" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                        <option value="FOB">FOB</option>
                        <option value="CMPTW">CMPTW</option>
                        <option value="CM">CM</option>
                    </select>
                </div>

                <!-- Division -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Division / Merchant Team *</label>
                    <input type="text" x-model="division" placeholder="e.g. Woven / Knit Team" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                </div>

                <!-- Dates & Currency -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">PO Received Date *</label>
                    <input type="date" x-model="poReceivedDate" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Requested Delivery Date *</label>
                    <input type="date" x-model="requestedDeliveryDate" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Advance Receive Date</label>
                    <input type="date" x-model="advanceReceiveDate" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Currency</label>
                    <select x-model="currency" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="BDT">BDT (৳)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ২. কালার ও সাইজ আইটেম ব্রেকডাউন সেকশন -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">2. Line Items (Color & Size Matrix)</h5>
                <button type="button" @click="addMatrixRow" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition">
                    + Add Breakdown Line
                </button>
            </div>

            <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-2/12">Color</th>
                            <th class="p-3 w-2/12">Size</th>
                            <th class="p-3 w-2/12">Plant (Factory)</th>
                            <th class="p-3 w-2/12">Shipping Point</th>
                            <th class="p-3 w-1.5/12 text-right">Unit Price</th>
                            <th class="p-3 w-1.5/12 text-right">Quantity (Pcs)</th>
                            <th class="p-3 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <template x-for="(row, index) in items" :key="index">
                            <tr>
                                <td class="p-2.5 pl-4">
                                    <select x-model="row.color" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2.5">
                                    <select x-model="row.size" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Size</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2.5">
                                    <input type="text" x-model="row.plant" placeholder="Plant Name" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5">
                                    <input type="text" x-model="row.shipping_point" placeholder="Port / Depot" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5">
                                    <input type="number" step="0.001" x-model.number="row.unit_price" placeholder="0.00" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5">
                                    <input type="number" min="1" x-model.number="row.quantity" placeholder="0" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeMatrixRow(index)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-700 text-xs">
                            <td colspan="4" class="p-3 pl-4 text-right text-[10px] uppercase tracking-wider text-slate-400">Total Order Volume & Calculated Value</td>
                            <td class="p-3 text-right font-mono text-slate-800 font-bold text-sm" x-text="currency + ' ' + grandTotalAmount.toFixed(2)"></td>
                            <td class="p-3 text-right font-mono text-indigo-600 font-bold text-sm" x-text="totalOrderQty.toLocaleString() + ' Pcs'"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ৩. সাবমিশন অ্যাকশন বার -->
        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <a href="#" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</a>
            <button type="submit" :disabled="isSaving" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-400 rounded-xl shadow-sm transition">
                <span x-text="isSaving ? 'Saving Sales Order...' : 'Confirm & Generate Sales Order'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function salesOrderApp(stylesData) {
    return {
        availableStyles: stylesData || [],
        selectedStyleId: '',
        buyerName: '',
        buyerId: '',
        
        // Header Fields
        salesOrg: 'House 57 Bangladesh',
        distributionChannel: 'Export',
        jobMode: 'FOB',
        division: 'Merchant Team',
        buyerPoNumber: '',
        shipToParty: '',
        poReceivedDate: new Date().toISOString().slice(0, 10),
        requestedDeliveryDate: '',
        advanceReceiveDate: '',
        currency: 'USD',
        
        isSaving: false,

        // Items Breakdown Matrix
        items: [
            { 
                color: '', 
                size: '', 
                plant: 'Main Factory Unit 1', 
                shipping_point: 'Chittagong Port', 
                unit_price: 0, 
                quantity: '' 
            }
        ],

        // Style Change handler
        onStyleChange() {
            const style = this.availableStyles.find(s => s.id == this.selectedStyleId);
            if (style) {
                this.buyerName = style.buyer ? style.buyer.name : 'N/A';
                this.buyerId = style.buyer ? style.buyer.id : null;
                const defaultPrice = style.costing ? parseFloat(style.costing.target_fob) : 0;
                
                // Set default unit price for initial item row
                this.items.forEach(item => {
                    if(!item.unit_price) item.unit_price = defaultPrice;
                });
            } else {
                this.buyerName = '';
                this.buyerId = null;
            }
        },

        addMatrixRow() {
            const defaultPrice = this.items[0]?.unit_price || 0;
            this.items.push({ 
                color: '', 
                size: '', 
                plant: 'Main Factory Unit 1', 
                shipping_point: 'Chittagong Port', 
                unit_price: defaultPrice, 
                quantity: '' 
            });
        },

        removeMatrixRow(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        get totalOrderQty() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
        },

        get grandTotalAmount() {
            return this.items.reduce((sum, item) => sum + ((parseInt(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
        },

        submitOrder() {
            if (!this.selectedStyleId || !this.buyerPoNumber || !this.requestedDeliveryDate || this.totalOrderQty <= 0) {
                alert("Please fill in all required fields and ensure order quantity is greater than 0.");
                return;
            }

            this.isSaving = true;

            // Prepare items array with style_id injected
            const formattedItems = this.items.map(item => ({
                style_id: this.selectedStyleId,
                color: item.color,
                size: item.size,
                plant: item.plant,
                shipping_point: item.shipping_point,
                unit_price: item.unit_price,
                quantity: item.quantity
            }));

            fetch("{{ route('tenant.purchase.buyer.sales-order-store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    sales_org: this.salesOrg,
                    distribution_channel: this.distributionChannel,
                    job_mode: this.jobMode,
                    division: this.division,
                    buyer_id: this.buyerId,
                    ship_to_party: this.shipToParty,
                    buyer_po_number: this.buyerPoNumber,
                    po_received_date: this.poReceivedDate,
                    advance_receive_date: this.advanceReceiveDate || null,
                    requested_delivery_date: this.requestedDeliveryDate,
                    currency: this.currency,
                    items: formattedItems
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSaving = false;
                if(data.success) {
                    alert("Sales Order created successfully!");
                    window.location.href = "{{ route('tenant.purchase.buyer.index') }}";
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                this.isSaving = false;
                console.error(err);
                alert("Network error or server exception occurred.");
            });
        }
    }
}
</script>
@endpush