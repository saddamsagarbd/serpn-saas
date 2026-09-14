@extends('layouts.tenant')
@section('title', 'Supplier Invoice Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">New Supplier Invoice (Vendor Bill)</h3>
            <p class="text-xs text-slate-500">Create official vendor invoice against received GRN and adjust Debit Notes automatically.</p>
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
    <!-- Header Info Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Invoice Header Info</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Select Supplier *</label>
                <span id="sub_total_text" class="font-bold">{{ $invoice->supplier->name ?? 'N/A' }}</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Select GRN Number *</label>
                <span id="sub_total_text" class="font-bold">{{ $invoice->grn->grn_no ?? 'N/A' }}</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Vendor Invoice No *</label>
                <span id="sub_total_text" class="font-bold">{{ $invoice->invoice_no }}</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Invoice Date *</label>
                <span id="sub_total_text" class="font-bold">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M, Y') }}</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Due Date *</label>                
                <span id="sub_total_text" class="font-bold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Items Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-bold text-slate-700 border-b pb-2">Invoice Line Items</h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="invoice_items_table">
                <thead>
                    <tr class="border-y border-slate-200 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">Item Details</th>
                        <th class="py-2.5 px-3 text-right">Quantity</th>
                        <th class="py-2.5 px-3 text-right">Unit Price</th>
                        <th class="py-2.5 px-3 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody id="grn_items_body" class="text-xs divide-y divide-slate-100">
                    @forelse($invoice->grn->items as $index => $item)
                        <tr>
                            <td class="py-3 px-3 font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-800 block">
                                    {{ $item->item->name ?? $item->grnItem->item->name ?? 'Item #'.$item->id }}
                                </span>
                                @if(isset($item->style->style_number))
                                    <span class="text-[10px] text-slate-400 block">Style: {{ $item->style->style_number }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-medium">{{ number_format($item->quantity_received, 2) }}</td>
                            <td class="py-3 px-3 text-right font-mono">{{ number_format($item->unit_price, 2) }} ৳</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-800">{{ number_format($item->total_amount, 2) }} ৳</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">No items registered under this invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Financial Calculation Section -->
        <div class="flex justify-end pt-4 border-t border-slate-100">
            <div class="w-full md:w-80 space-y-2 text-xs font-mono">
                <div class="flex justify-between items-center text-slate-600">
                    <span>Sub Total:</span>
                    <span id="sub_total_text" class="font-bold">{{ number_format($invoice->sub_total, 2) }} ৳</span>
                </div>

                <div class="flex justify-between items-center text-slate-600">
                    <span>Tax Rate (%):</span>                    
                    <span id="tax_amount_text">{{ number_format($invoice->tax_rate, 1) }}%</span>
                </div>

                <div class="flex justify-between items-center text-slate-600">
                    <span>Tax Amount:</span>
                    <span id="tax_amount_text">{{ number_format($invoice->tax_amount, 2) }} ৳</span>
                </div>

                <div class="flex justify-between items-center text-slate-600">
                    <span>Discount Amount:</span>
                    <span id="tax_amount_text">{{ number_format($invoice->discount_amount, 2) }} ৳</span>
                </div>

                <div class="flex justify-between items-center text-rose-600 font-bold">
                    <span>Debit Note Deduction (PR):</span>
                    <span>- <span id="debit_note_text">{{ number_format($invoice->debit_note_adjusted_amount, 2) }}</span> ৳</span>
                </div>

                @php
                    $subTotal = $invoice->sub_total ?? 0;
                    $tax = $invoice->tax_amount ?? 0;
                    $discount = $invoice->discount_amount ?? 0;
                    $debitNote = $invoice->debit_note_adjusted_amount ?? 0;
                    $calculatedNetTotal = max(0, ($subTotal + $tax) - ($discount + $debitNote));
                @endphp

                <div class="flex justify-between items-center text-sm font-bold text-indigo-600 pt-2 border-t border-slate-200">
                    <span>Net Amount:</span>
                    <span id="net_amount_text">{{ number_format($calculatedNetTotal, 2) }} ৳</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

});
</script>
@endsection