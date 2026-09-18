@extends('layouts.tenant')
@section('title', isset($invoice) ? 'Supplier Invoice Edit' : 'Supplier Invoice Create')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">
                {{ isset($invoice) ? 'Edit Supplier Invoice' : 'New Supplier Invoice (Vendor Bill)' }}
            </h3>
            <p class="text-xs text-slate-500">
                {{ isset($invoice) ? 'Update details of existing invoice.' : 'Create official vendor invoice against received GRN and adjust Debit Notes automatically.' }}
            </p>
        </div>
        <a href="{{ route('tenant.purchase.invoice.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
            ← Back to Invoices
        </a>
    </div>

    <!-- 🚨 Global Error Alert Box -->
    <div id="form_error_alert" class="hidden p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700">
        <div class="flex items-center gap-2 font-bold mb-1">
            <svg class="w-4 h-4 fill-current text-rose-600" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
            <span>Please fix the following validation errors:</span>
        </div>
        <ul id="error_list" class="list-disc pl-5 space-y-1 text-xs"></ul>
    </div>

    <!-- Dynamic Form Action (Store / Update) -->
    <form id="invoice_form" 
          action="{{ isset($invoice) ? route('tenant.purchase.invoice.update', $invoice->id) : route('tenant.purchase.invoice.store') }}" 
          method="POST" 
          class="space-y-6">
        @csrf
        @if(isset($invoice))
            @method('PUT')
        @endif
        
        <!-- Header Info Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Invoice Header Info</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ (isset($invoice) && $invoice->supplier_id == $supplier->id) ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select GRN Number *</label>
                    <select name="grn_id" id="goods_received_note_id" {{ !isset($invoice) ? 'disabled' : '' }} required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select GRN --</option>
                        @foreach($grns as $grn)
                            <option value="{{ $grn->id }}" 
                                    data-supplier="{{ $grn->supplier_id }}"
                                    {{ (isset($invoice) && $invoice->goods_received_note_id == $grn->id) ? 'selected' : '' }}>
                                {{ $grn->grn_no }} (PO: {{ $grn->purchaseOrder->po_no ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Vendor Invoice No *</label>
                    <input type="text" 
                        name="invoice_no" 
                        id="invoice_no" 
                        value="{{ $invoice->invoice_no ?? $nextInvoiceNo }}" 
                        readonly 
                        class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl font-mono text-slate-600 font-bold cursor-not-allowed focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ isset($invoice) ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Due Date *</label>
                    <input type="date" name="due_date" value="{{ isset($invoice) ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Invoice Line Items</h4>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="invoice_items_table">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b">
                            <th class="p-3">Item Description</th>
                            <th class="p-3 text-right">Billed Qty *</th>
                            <th class="p-3 text-right">Unit Price *</th>
                            <th class="p-3 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody id="grn_items_body" class="text-xs divide-y divide-slate-100">
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400 italic">Please select Supplier and GRN to populate received items.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Financial Calculation Section -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <div class="w-full md:w-80 space-y-2 text-xs font-mono">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Sub Total:</span>
                        <span id="sub_total_text" class="font-bold">{{ number_format($invoice->subtotal ?? 0, 2) }} ৳</span>
                        <input type="hidden" name="sub_total" id="sub_total_input" value="{{ $invoice->subtotal ?? 0 }}">
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Tax Rate (%):</span>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" value="{{ $invoice->tax_rate ?? '' }}" class="calc-trigger w-20 px-2 py-1 text-right border border-slate-200 rounded-lg text-xs font-mono" placeholder="0">
                            <span class="text-xs font-bold text-slate-500">%</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-slate-500 text-[11px] pl-2">
                        <span>Tax Amount:</span>
                        <span id="tax_amount_text">{{ number_format($invoice->tax_amount ?? 0, 2) }} ৳</span>
                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ $invoice->tax_amount ?? 0 }}">
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Discount Amount:</span>
                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" value="{{ $invoice->discount_amount ?? '' }}" class="calc-trigger w-28 px-2 py-1 text-right border border-slate-200 rounded-lg text-xs font-mono" placeholder="0.00">
                    </div>

                    <div class="flex justify-between items-center text-rose-600 font-bold">
                        <span>Debit Note Deduction (PR):</span>
                        <span>- <span id="debit_note_text">{{ number_format($invoice->debit_note_adjusted_amount ?? 0, 2) }}</span> ৳</span>
                        <input type="hidden" name="debit_note_adjusted_amount" id="debit_note_adjusted_amount" value="{{ $invoice->debit_note_adjusted_amount ?? 0 }}">
                    </div>

                    <div class="flex justify-between items-center text-sm font-bold text-indigo-600 pt-2 border-t border-slate-200">
                        <span>Net Amount:</span>
                        <span id="net_amount_text">{{ number_format($invoice->grand_total ?? 0, 2) }} ৳</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" id="submit_btn" {{ !isset($invoice) ? 'disabled' : '' }} class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition disabled:opacity-50">
                {{ isset($invoice) ? 'Update Invoice' : 'Save & Post Invoice' }}
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('invoice_form');
    const supplierSelect = document.getElementById('supplier_id');
    const grnSelect = document.getElementById('goods_received_note_id');
    const tableBody = document.getElementById('grn_items_body');
    const submitBtn = document.getElementById('submit_btn');
    const errorAlert = document.getElementById('form_error_alert');
    const errorList = document.getElementById('error_list');

    const allGrns = @json($grns);
    const existingInvoice = @json($invoice ?? null);

    // Initial Load Check for Edit Mode
    if (existingInvoice) {
        fetchGrnAndRender(existingInvoice.goods_received_note_id, existingInvoice.items);
    }

    // Filter GRN by Supplier
    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;
        grnSelect.innerHTML = '<option value="">-- Select GRN --</option>';
        tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400 italic">Please select GRN to populate items.</td></tr>`;
        resetCalculations();

        if (supplierId) {
            grnSelect.disabled = false;
            allGrns.forEach(grn => {
                if (grn.supplier_id == supplierId) {
                    const opt = document.createElement('option');
                    opt.value = grn.id;
                    opt.textContent = `${grn.grn_no} (PO: ${grn.purchase_order ? grn.purchase_order.po_no : 'N/A'})`;
                    grnSelect.appendChild(opt);
                }
            });
        } else {
            grnSelect.disabled = true;
        }
    });

    // On GRN Change
    grnSelect.addEventListener('change', function() {
        const grnId = this.value;
        if (!grnId) {
            tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400 italic">Please select GRN to populate items.</td></tr>`;
            resetCalculations();
            return;
        }
        fetchGrnAndRender(grnId);
    });

    // Fetch GRN & Debit Note Data
    function fetchGrnAndRender(grnId, savedItems = null) {
        tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-500 font-bold">Loading GRN details...</td></tr>`;

        fetch(`{{ route('tenant.purchase.invoice.get-grn-data') }}?goods_received_note_id=${grnId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success === true) {
                    renderItems(data.grn.items, savedItems);
                    
                    if (!savedItems) {
                        const debitNoteAmt = parseFloat(data.debit_note_amount) || 0;
                        document.getElementById('debit_note_adjusted_amount').value = debitNoteAmt;
                        document.getElementById('debit_note_text').innerText = debitNoteAmt.toFixed(2);
                    }
                    
                    calculateTotals();
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error(error);
                tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-rose-500 font-bold">Failed to load items.</td></tr>`;
            });
    }

    function renderItems(items, savedItems = null) {
        tableBody.innerHTML = '';
        items.forEach((item, index) => {
            const itemName = (item.item && item.item.name) ? item.item.name : 'Item #' + item.item_id;
            
            // If editing, find saved qty for this item
            let currentQty = item.billable_qty ?? item.quantity_received;
            if (savedItems) {
                const match = savedItems.find(s => s.goods_received_note_item_id == item.id || s.grn_item_id == item.id);
                if (match) currentQty = match.quantity;
            }

            const row = `
                <tr class="item-row hover:bg-slate-50/50">
                    <td class="p-3 font-bold text-slate-800">
                        ${itemName}
                        <input type="hidden" name="items[${index}][grn_item_id]" value="${item.id}">
                    </td>
                    <td class="p-3 text-right">
                        <input type="number" step="0.01" name="items[${index}][invoice_qty]" value="${currentQty}" required class="qty-input calc-trigger w-28 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-slate-700">
                    </td>
                    <td class="p-3 text-right">
                        <input type="number" step="0.01" value="${item.unit_price}" readonly class="price-input w-28 px-2 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs text-right font-mono text-slate-600 cursor-not-allowed">
                    </td>
                    <td class="p-3 text-right font-mono font-bold text-slate-800">
                        <span class="line-total">0.00</span> ৳
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        bindEvents();
    }

    function bindEvents() {
        document.querySelectorAll('.calc-trigger').forEach(input => {
            input.removeEventListener('input', calculateTotals);
            input.addEventListener('input', calculateTotals);
        });
    }

    function calculateTotals() {
        let subTotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || 0);
            const price = parseFloat(row.querySelector('.price-input')?.value || 0);
            const total = qty * price;

            row.querySelector('.line-total').innerText = total.toFixed(2);
            subTotal += total;
        });

        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const debitNote = parseFloat(document.getElementById('debit_note_adjusted_amount').value) || 0;

        const calculatedTaxAmount = (subTotal * taxRate) / 100;
        const netAmount = Math.max(0, (subTotal + calculatedTaxAmount) - (discount + debitNote));

        document.getElementById('tax_amount').value = calculatedTaxAmount.toFixed(2);
        document.getElementById('tax_amount_text').innerText = calculatedTaxAmount.toFixed(2) + ' ৳';

        document.getElementById('sub_total_input').value = subTotal;
        document.getElementById('sub_total_text').innerText = subTotal.toFixed(2) + ' ৳';
        document.getElementById('net_amount_text').innerText = netAmount.toFixed(2) + ' ৳';
    }

    function resetCalculations() {
        document.getElementById('sub_total_text').innerText = '0.00 ৳';
        document.getElementById('debit_note_text').innerText = '0.00';
        document.getElementById('net_amount_text').innerText = '0.00 ৳';
        submitBtn.disabled = true;
    }

    // 🚀 AJAX Form Submission Handler (Supports POST & PUT)
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        errorAlert.classList.add('hidden');
        errorList.innerHTML = '';
        submitBtn.disabled = true;
        const btnDefaultText = existingInvoice ? 'Update Invoice' : 'Save & Post Invoice';
        submitBtn.innerText = existingInvoice ? 'Updating Invoice...' : 'Posting Invoice...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST', // Handles both POST and PUT (_method: 'PUT' sent inside FormData)
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw data;
            return data;
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url || "{{ route('tenant.purchase.invoice.index') }}";
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerText = btnDefaultText;

            let messages = [];

            if (error.errors) {
                Object.values(error.errors).forEach(errArray => {
                    errArray.forEach(msg => messages.push(msg));
                });
            } else if (error.message) {
                messages.push(error.message);
            } else {
                messages.push('Something went wrong. Please try again.');
            }

            errorList.innerHTML = messages.map(msg => `<li>${msg}</li>`).join('');
            errorAlert.classList.remove('hidden');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
});
</script>
@endsection