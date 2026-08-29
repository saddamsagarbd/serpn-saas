@extends('layouts.tenant')
@section('title', 'Supplier Invoice Create')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">New Supplier Invoice (Vendor Bill)</h3>
            <p class="text-xs text-slate-500">Create official vendor invoice against received GRN and adjust Debit Notes automatically.</p>
        </div>
        <a href="{{ route('tenant.purchase.suppliers.invoice.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
            ← Back to Invoices
        </a>
    </div>

    <form action="{{ route('tenant.purchase.suppliers.invoice.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Header Info Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Invoice Header Info</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select GRN Number *</label>
                    <select name="goods_received_note_id" id="goods_received_note_id" disabled required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select GRN --</option>
                        @foreach($grns as $grn)
                            <option value="{{ $grn->id }}" data-supplier="{{ $grn->supplier_id }}">
                                {{ $grn->grn_no }} (PO: {{ $grn->purchaseOrder->po_no ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Vendor Invoice No *</label>
                    <input type="text" name="invoice_no" required placeholder="e.g. INV-2026-001" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:outline-none focus:border-indigo-500">
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
                        <span id="sub_total_text" class="font-bold">0.00 ৳</span>
                        <input type="hidden" name="sub_total" id="sub_total_input" value="0">
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Tax Amount:</span>
                        <input type="number" step="0.01" name="tax_amount" id="tax_amount" value="0.00" class="calc-trigger w-28 px-2 py-1 text-right border border-slate-200 rounded-lg text-xs font-mono">
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Discount Amount:</span>
                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" value="0.00" class="calc-trigger w-28 px-2 py-1 text-right border border-slate-200 rounded-lg text-xs font-mono">
                    </div>

                    <div class="flex justify-between items-center text-rose-600 font-bold">
                        <span>Debit Note Deduction (PR):</span>
                        <span>- <span id="debit_note_text">0.00</span> ৳</span>
                        <input type="hidden" name="debit_note_adjusted_amount" id="debit_note_adjusted_amount" value="0">
                    </div>

                    <div class="flex justify-between items-center text-sm font-bold text-indigo-600 pt-2 border-t border-slate-200">
                        <span>Net Amount:</span>
                        <span id="net_amount_text">0.00 ৳</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" id="submit_btn" disabled class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition disabled:opacity-50">
                Save & Post Invoice
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const grnSelect = document.getElementById('goods_received_note_id');
    const tableBody = document.getElementById('grn_items_body');
    const submitBtn = document.getElementById('submit_btn');

    // All GRNs passed from controller
    const allGrns = @json($grns);

    // Supplier পরিবর্তনের ওপর ভিত্তি করে GRN Filter
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

    // AJAX Call: GRN সিলেক্ট করলে Item এবং Debit Note লোড
    grnSelect.addEventListener('change', function() {
        const grnId = this.value;

        if (!grnId) {
            tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400 italic">Please select GRN to populate items.</td></tr>`;
            resetCalculations();
            return;
        }

        tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-500 font-bold">Loading GRN details...</td></tr>`;

        fetch(`{{ route('tenant.purchase.suppliers.invoice.get-grn-data') }}?goods_received_note_id=${grnId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderItems(data.grn.items);
                    document.getElementById('debit_note_adjusted_amount').value = data.debit_note_amount;
                    document.getElementById('debit_note_text').innerText = parseFloat(data.debit_note_amount).toFixed(2);
                    calculateTotals();
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error(error);
                tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-rose-500 font-bold">Failed to load items.</td></tr>`;
            });
    });

    function renderItems(items) {
        tableBody.innerHTML = '';
        items.forEach((item, index) => {
            const itemName = (item.item_master && item.item_master) ? item.item_master.name : 'Item #' + item.item_id;
            const itemId = (item.item_master && item.item_master.id) ? item.item_master.id : item.item_id;

            const row = `
                <tr class="item-row hover:bg-slate-50/50">
                    <td class="p-3 font-bold text-slate-800">
                        ${itemName}
                        <input type="hidden" name="items[${index}][item_id]" value="${itemId}">
                    </td>
                    <td class="p-3 text-right">
                        <input type="number" step="0.01" name="items[${index}][quantity]" value="${item.quantity_received}" required class="qty-input calc-trigger w-28 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-slate-700">
                    </td>
                    <td class="p-3 text-right">
                        <input type="number" step="0.01" name="items[${index}][unit_price]" value="${item.unit_price}" required class="price-input calc-trigger w-28 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right font-mono text-slate-700">
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

        const tax = parseFloat(document.getElementById('tax_amount').value || 0);
        const discount = parseFloat(document.getElementById('discount_amount').value || 0);
        const debitNote = parseFloat(document.getElementById('debit_note_adjusted_amount').value || 0);

        const netAmount = Math.max(0, (subTotal + tax) - (discount + debitNote));

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
});
</script>
@endsection