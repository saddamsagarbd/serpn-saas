@extends('layouts.tenant')
@section('title', 'Create Purchase Return / Debit Note')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">New Purchase Return (Debit Note)</h3>
            <p class="text-xs text-slate-500">Return damaged or rejected materials back to supplier and adjust stock & ledger.</p>
        </div>
        <a href="{{ route('tenant.purchase.return') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
            ← Back to Returns
        </a>
    </div>

    <form action="{{ route('tenant.purchase.return.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Header Info Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Return Reference Info</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select GRN Number *</label>
                    <select name="grn_id" id="grn_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Received GRN --</option>
                        @foreach($grns as $grn)
                            <option value="{{ $grn->id }}" data-warehouse="{{ $grn->warehouse_id }}" data-supplier="{{ $grn->supplier->name ?? 'N/A' }}">
                                {{ $grn->grn_no }} (PO: {{ $grn->purchaseOrder->po_no ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Return Date *</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Warehouse *</label>
                    <select name="warehouse_id" id="warehouse_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Reason for Return</label>
                <input type="text" name="reason" placeholder="e.g. Fabric Shade Variation / Damaged during transport" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Return Line Items</h4>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="return_items_table">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b">
                            <th class="p-3">Item Description</th>
                            <th class="p-3 text-right">Received Qty</th>
                            <th class="p-3 text-right">Unit Price</th>
                            <th class="p-3 text-right">Return Qty *</th>
                            <th class="p-3 text-right">Subtotal</th>
                            <th class="p-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="grn_items_body" class="text-xs divide-y divide-slate-100">
                        <tr class="item-row">
                            <td class="p-2">
                                <input type="text" name="items[0][item_id]" placeholder="Item Variant ID" required class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs font-mono">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="items[0][unit_price]" value="0.00" required class="unit-price w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-right font-mono">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="items[0][return_qty]" value="1" min="0.01" required class="return-qty w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-rose-600">
                            </td>
                            <td class="p-2">
                                <input type="text" readonly value="0.00" class="line-total w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-slate-700">
                            </td>
                            <td class="p-2">
                                <input type="text" name="items[0][remarks]" placeholder="Defect reason" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" class="remove-row text-rose-500 hover:text-rose-700 font-bold px-2 py-1">✕</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end items-center pt-2">
                <!-- <button type="button" id="add_item_btn" class="px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                    + Add Another Item
                </button> -->
                <div class="text-right font-mono">
                    <span class="text-xs font-bold text-slate-500 uppercase">Grand Total Return Amount: </span>
                    <span id="grand_total_text" class="text-sm font-bold text-rose-600 pl-2">0.00</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm transition">
                Process Return
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const grnSelect = document.getElementById('grn_id');
    const tableBody = document.getElementById('grn_items_body');
    const warehouseIdInput = document.getElementById('warehouse_id');

    // Calculate Row Total & Grand Total
    function calculateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.return-qty').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const total = qty * price;
            row.querySelector('.line-total').value = total.toFixed(2);
            grandTotal += total;
        });
        document.getElementById('grand_total_text').innerText = grandTotal.toFixed(2) + ' ৳';
    }

    document.getElementById('return_items_table').addEventListener('input', calculateTotals);

    // Remove Row
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        }
    });

    grnSelect.addEventListener('change', function() {
        const grnId = this.value;

        if (!grnId) {
            tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-slate-400 italic">Please select a GRN above to populate received items.</td></tr>`;
            calculateTotals();
            return;
        }

        // Show Loading State
        tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-slate-500 font-bold">Loading GRN items...</td></tr>`;

        // Fetch Data via AJAX
        fetch(`/purchase/grn/${grnId}/items`)
            .then(response => response.json())
            .then(data => {
                warehouseIdInput.value = data.warehouse_id;
                
                tableBody.innerHTML = '';

                if (data.items.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-slate-400">No items found in this GRN.</td></tr>`;
                    return;
                }

                // Render GRN Items as static rows with editable return Qty
                data.items.forEach((item, index) => {
                    const row = `
                        <tr class="item-row hover:bg-slate-50/50">
                            <td class="p-3 font-bold text-slate-800">
                                ${item.item_name}
                                <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                            </td>
                            <td class="p-3 text-right font-mono text-slate-500">${item.received_qty}</td>
                            <td class="p-3 text-right font-mono text-slate-600">
                                ${parseFloat(item.unit_price).toFixed(2)}
                                <input type="hidden" name="items[${index}][unit_price]" class="unit-price" value="${item.unit_price}">
                            </td>
                            <td class="p-3 text-right">
                                <input type="number" step="0.01" name="items[${index}][return_qty]" value="0" min="0" max="${item.received_qty}" required class="return-qty w-28 px-2 py-1 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-rose-600 focus:outline-none focus:border-rose-500">
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-slate-800">
                                <input type="text" readonly value="0.00" class="line-total w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-right font-mono font-bold text-slate-700">
                            </td>
                            <td class="p-3">
                                <input type="text" name="items[${index}][remarks]" placeholder="Reason for item return" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-xs">
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                calculateTotals();
            })
            .catch(error => {
                console.error('Error fetching GRN items:', error);
                tableBody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-rose-500 font-bold">Failed to load GRN items. Please try again.</td></tr>`;
            });
    });
});
</script>
@endsection