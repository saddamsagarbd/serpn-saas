@extends('layouts.tenant')

@section('content')
<div class="p-6" x-data="supplierInvoiceEngine()">
    <!-- Top Bar -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Supplier Invoice (Bill) Generation</h1>
            <p class="text-sm text-slate-500">3-Way Matching against Goods Received Notes (GRN)</p>
        </div>
        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">ERP Billing Module</span>
    </div>

    <form action="{{ route('tenant.purchase.invoices.store') }}" method="POST">
        @csrf
        
        <!-- Header Reference Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Select GRN Number *</label>
                    <select name="grn_id" x-model="selectedGrnId" @change="fetchGrnData()" class="w-full border-slate-300 rounded-lg text-sm focus:ring-purple-500" required>
                        <option value="">-- Choose GRN --</option>
                        @foreach($grns as $grn)
                            <option value="{{ $grn->id }}">{{ $grn->grn_no }} (PO: {{ $grn->purchaseOrder->po_no ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Supplier Bill/Invoice No *</label>
                    <input type="text" name="invoice_no" placeholder="e.g. INV-HA-2026-99" class="w-full border-slate-300 rounded-lg text-sm focus:ring-purple-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg text-sm focus:ring-purple-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Payment Due Date *</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border-slate-300 rounded-lg text-sm focus:ring-purple-500" required>
                </div>
            </div>
        </div>

        <!-- Line Items Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="bg-slate-900 px-6 py-3 text-white flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wider">Item Billing Details (Auto-Deducted Returned Stock)</span>
                <span class="text-xs text-slate-400" x-text="items.length + ' Items Loaded'"></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] border-b">
                        <tr>
                            <th class="p-4">Item Description</th>
                            <th class="p-4">Color / Size</th>
                            <th class="p-4 text-center">GRN Received</th>
                            <th class="p-4 text-center">Returned Qty</th>
                            <th class="p-4 text-center">Billable Qty</th>
                            <th class="p-4 text-right">Unit Price</th>
                            <th class="p-4 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-4 font-medium text-slate-800">
                                    <input type="hidden" :name="`items[${index}][grn_item_id]`" :value="item.grn_item_id">
                                    <span x-text="item.item_name"></span>
                                    <span class="block text-xs text-slate-400" x-text="'Code: ' + item.item_code"></span>
                                </td>
                                <td class="p-4 text-slate-600" x-text="item.color_name + ' / ' + item.size_name"></td>
                                <td class="p-4 text-center font-semibold text-slate-700" x-text="item.received_qty"></td>
                                <td class="p-4 text-center font-semibold text-red-500" x-text="item.returned_qty"></td>
                                <td class="p-4 text-center">
                                    <input type="number" step="0.01" :name="`items[${index}][invoice_qty]`" x-model.number="item.billable_qty" @input="calculateTotals()" class="w-24 border-slate-300 rounded-lg text-center font-bold text-slate-800 focus:ring-purple-500">
                                </td>
                                <td class="p-4 text-right text-slate-600" x-text="formatCurrency(item.unit_price)"></td>
                                <td class="p-4 text-right font-bold text-slate-900" x-text="formatCurrency(item.billable_qty * item.unit_price)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Totaling & Calculation Panel -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Invoice Remarks / Notes</label>
                    <textarea name="remarks" rows="3" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Add optional payment terms or remarks..."></textarea>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal:</span>
                        <span class="font-semibold text-slate-800" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm items-center">
                        <span class="text-slate-600">Other Charges (+):</span>
                        <input type="number" step="0.01" name="other_charges" x-model.number="otherCharges" @input="calculateTotals()" class="w-32 border-slate-300 rounded-lg text-right text-sm">
                    </div>
                    <div class="flex justify-between text-sm items-center">
                        <span class="text-slate-600">Discount (-):</span>
                        <input type="number" step="0.01" name="discount_amount" x-model.number="discount" @input="calculateTotals()" class="w-32 border-slate-300 rounded-lg text-right text-sm">
                    </div>
                    <div class="flex justify-between text-base font-bold text-slate-900 border-t pt-2">
                        <span>Grand Total Payable:</span>
                        <span class="text-purple-700 text-lg" x-text="formatCurrency(grandTotal)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-bold shadow-md transition">
                Create & Post Supplier Invoice
            </button>
        </div>
    </form>
</div>

<script>
function supplierInvoiceEngine() {
    return {
        selectedGrnId: '',
        items: [],
        subtotal: 0,
        otherCharges: 0,
        discount: 0,
        grandTotal: 0,

        fetchGrnData() {
            if (!this.selectedGrnId) {
                this.items = [];
                this.calculateTotals();
                return;
            }

            fetch(`/tenant/purchase/grn-details/${this.selectedGrnId}`)
                .then(res => res.json())
                .then(data => {
                    this.items = data.items;
                    this.calculateTotals();
                });
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + ((item.billable_qty || 0) * item.unit_price), 0);
            this.grandTotal = (this.subtotal + (this.otherCharges || 0)) - (this.discount || 0);
        },

        formatCurrency(val) {
            return (val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ৳';
        }
    }
}
</script>
@endsection