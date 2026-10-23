@extends('layouts.tenant')
@section('title', 'Make Supplier Payment')

@section('content')
<div class="space-y-6" x-data="supplierPaymentForm()">
    
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-gray-800">New Supplier Payment</h2>
                <p class="text-xs text-slate-400 mt-0.5">Pay vendor bills, record disbursements, and adjust advance balances.</p>
            </div>
            <a href="{{ route('tenant.purchase.invoice.payment') }}" class="bg-gray-100 text-gray-600 font-bold text-xs px-4 py-2 rounded-xl hover:bg-gray-200 transition">
                Back to List
            </a>
        </div>

        <form action="{{ route('tenant.purchase.invoice.payment.store') }}" method="POST" class="space-y-6 pt-6">
            @csrf

            <!-- Payment Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50/60 rounded-xl border border-gray-200/60">
                
                <!-- Select Supplier -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Supplier *</label>
                    <select x-model="selectedSupplierId" @change="fetchSupplierData()" name="supplier_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-rose-500" required>
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bank / Cash Account -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Paid From (Bank/Cash) *</label>
                    <select name="bank_account_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-rose-500" required>
                        <option value="">-- Select Bank or Cash Account --</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Date -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-rose-500" required>
                </div>

                <!-- Payment Method -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Payment Method *</label>
                    <select name="payment_method" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-rose-500" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="cash">Cash</option>
                        <option value="mobile_banking">Mobile Banking</option>
                    </select>
                </div>

            </div>

            <!-- Amount & Advance Adjustment Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-rose-50/40 rounded-xl border border-rose-100">
                
                <!-- Bank Paid Amount -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Cash/Bank Paid Amount *</label>
                    <input type="number" step="0.01" x-model.number="cashPaidAmount" name="paid_amount" placeholder="0.00" class="w-full px-3 py-2 text-sm font-bold bg-white border border-slate-200 rounded-xl text-emerald-600 focus:outline-none focus:border-rose-500" required>
                </div>

                <!-- Available Advance Info & Input -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Use Available Advance</label>
                        <span class="text-[11px] font-bold text-rose-600" x-text="'Available: ' + formatCurrency(availableAdvanceBalance)"></span>
                    </div>
                    <input type="number" step="0.01" x-model.number="advanceAmountToUse" name="advance_amount_to_use" :max="availableAdvanceBalance" placeholder="0.00" class="w-full px-3 py-2 text-sm font-bold bg-white border border-slate-200 rounded-xl text-rose-600 focus:outline-none focus:border-rose-500">
                </div>

                <!-- Reference No -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Reference / Cheque No</label>
                    <input type="text" name="reference_no" placeholder="e.g. CHQ-998231" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-rose-500">
                </div>

            </div>

            <!-- Unpaid Invoices Table (FIFO Allocation Preview) -->
            <div class="space-y-3 pt-2">
                <div class="flex justify-between items-center">
                    <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Unpaid Invoices (FIFO Allocation Preview)</h5>
                    <span class="text-xs font-bold text-slate-600" x-text="'Total Effective Payment: ' + formatCurrency(totalEffectivePayment)"></span>
                </div>
                
                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="p-3 pl-4">Invoice No</th>
                                <th class="p-3">Invoice Date</th>
                                <th class="p-3 text-right">Grand Total</th>
                                <th class="p-3 text-right">Due Amount</th>
                                <th class="p-3 text-right">Allocating Amount</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                            <template x-if="loadingInvoices">
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-rose-600 font-semibold animate-pulse">
                                        Loading supplier details & unpaid invoices...
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loadingInvoices && unpaidInvoices.length === 0">
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 font-medium">
                                        Please select a supplier to load unpaid invoices.
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loadingInvoices && unpaidInvoices.length > 0">
                                <template x-for="(inv, index) in unpaidInvoices" :key="inv.id">
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3 pl-4 font-bold text-rose-600" x-text="inv.invoice_no"></td>
                                        <td class="p-3 text-slate-600" x-text="inv.invoice_date"></td>
                                        <td class="p-3 text-right font-mono" x-text="formatCurrency(inv.net_amount)"></td>
                                        <td class="p-3 text-right font-mono font-bold text-slate-800" x-text="formatCurrency(inv.net_amount - inv.paid_amount)"></td>
                                        
                                        <td class="p-3 text-right font-mono font-bold text-emerald-600">
                                            <span x-text="formatCurrency(calculateAllocation(index))"></span>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                    Save & Post Payment Voucher
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function supplierPaymentForm() {
    return {
        selectedSupplierId: '',
        cashPaidAmount: 0,
        advanceAmountToUse: 0,
        availableAdvanceBalance: 0,
        unpaidInvoices: [],
        loadingInvoices: false,

        get totalEffectivePayment() {
            return (parseFloat(this.cashPaidAmount) || 0) + (parseFloat(this.advanceAmountToUse) || 0);
        },

        fetchSupplierData() {
            if (!this.selectedSupplierId) {
                this.unpaidInvoices = [];
                this.availableAdvanceBalance = 0;
                this.advanceAmountToUse = 0;
                return;
            }

            this.loadingInvoices = true;
            fetch(`/purchase/suppliers/${this.selectedSupplierId}/unpaid-invoices`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                this.unpaidInvoices = data.invoices || [];
                this.availableAdvanceBalance = parseFloat(data.available_advance) || 0;
                this.loadingInvoices = false;
            })
            .catch(err => {
                console.error('Error fetching supplier data:', err);
                this.loadingInvoices = false;
            });
        },

        calculateAllocation(index) {
            let remaining = this.totalEffectivePayment;
            for (let i = 0; i < this.unpaidInvoices.length; i++) {
                let due = parseFloat(this.unpaidInvoices[i].due_amount) || 0;
                let allocated = Math.min(remaining, due);
                if (i === index) {
                    return allocated.toFixed(2);
                }
                remaining -= allocated;
            }
            return '0.00';
        },

        formatCurrency(amount) {
            return parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ৳';
        }
    }
}
</script>
@endpush