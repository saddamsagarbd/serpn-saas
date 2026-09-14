<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_{{ $invoice->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11px; }
            .print-padding { padding: 0 !important; }
            .print-border { border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans print-padding p-6">

    <!-- Top Action Bar (Print/Back Button) -->
    <div class="max-w-4xl mx-auto mb-4 flex justify-between items-center no-print">
        <a href="{{ route('tenant.purchase.invoice.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-800 flex items-center gap-1">
            ← Back to Invoices
        </a>
        <button onclick="window.print()" class="bg-rose-600 text-white font-bold text-xs px-5 py-2 rounded-lg hover:bg-rose-700 shadow-md transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Invoice
        </button>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl">
        
        <!-- Header / Company Info -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">SUPPLIER INVOICE</h1>
                <p class="text-xs font-bold text-rose-600 mt-0.5">Accounts Payable Record</p>
                <div class="mt-3 text-xs text-slate-500 space-y-0.5">
                    <p class="font-bold text-slate-800">{{ tenant('company_name') ?? 'Your Company Name' }}</p>
                    <p>{{ tenant('address') ?? 'Factory / Head Office Address' }}</p>
                    <p>Email: {{ tenant('email') ?? 'support@company.com' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2
                    {{ $invoice->status == 'paid' ? 'bg-emerald-100 text-emerald-800' : ($invoice->status == 'partially_paid' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    STATUS: {{ str_replace('_', ' ', $invoice->status) }}
                </span>
                <p class="text-xs text-slate-400 font-mono">Invoice #: <span class="font-bold text-slate-800">{{ $invoice->invoice_no }}</span></p>
                <p class="text-xs text-slate-400 font-mono">Invoice Date: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M, Y') }}</span></p>
                <p class="text-xs text-slate-400 font-mono">Due Date: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</span></p>
            </div>
        </div>

        <!-- Supplier & Reference Info Grid -->
        <div class="grid grid-cols-2 gap-6 my-6 p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs">
            <div>
                <h4 class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Vendor Details (Bill From):</h4>
                <p class="font-bold text-slate-800 text-sm">{{ $invoice->supplier->name ?? 'N/A' }}</p>
                <p class="text-slate-600">{{ $invoice->supplier->address ?? 'N/A' }}</p>
                <p class="text-slate-600">Phone: {{ $invoice->supplier->phone ?? 'N/A' }}</p>
                <p class="text-slate-600">Email: {{ $invoice->supplier->email ?? 'N/A' }}</p>
            </div>
            <div class="border-l border-slate-200 pl-6 space-y-1">
                <h4 class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Audit References:</h4>
                <p><span class="text-slate-400">GRN Ref:</span> <span class="font-bold text-slate-700">{{ $invoice->grn->grn_no ?? 'N/A' }}</span></p>
                <p><span class="text-slate-400">PO Ref:</span> <span class="font-bold text-slate-700">{{ $invoice->purchaseOrder->po_no ?? 'N/A' }}</span></p>
                <p><span class="text-slate-400">Voucher Ref:</span> <span class="font-bold text-slate-700">{{ $invoice->voucher->voucher_no ?? 'N/A' }}</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left border-collapse my-6">
            <thead>
                <tr class="border-y border-slate-200 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-2.5 px-3">#</th>
                    <th class="py-2.5 px-3">Item Details</th>
                    <th class="py-2.5 px-3 text-right">Quantity</th>
                    <th class="py-2.5 px-3 text-right">Unit Price</th>
                    <th class="py-2.5 px-3 text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
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

        <!-- Accounting Calculation Breakdown -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold">{{ number_format($invoice->sub_total, 2) }} ৳</span>
                </div>
                
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax / VAT ({{ number_format($invoice->tax_rate, 1) }}%):</span>
                        <span class="font-mono">+ {{ number_format($invoice->tax_amount, 2) }} ৳</span>
                    </div>
                @endif

                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount:</span>
                        <span class="font-mono">- {{ number_format($invoice->discount_amount, 2) }} ৳</span>
                    </div>
                @endif

                @if($invoice->debit_note_adjusted_amount > 0)
                    <div class="flex justify-between text-rose-700">
                        <span>Debit Note Adjusted:</span>
                        <span class="font-mono">- {{ number_format($invoice->debit_note_adjusted_amount, 2) }} ৳</span>
                    </div>
                @endif

                @php
                    $subTotal = $invoice->sub_total ?? 0;
                    $tax = $invoice->tax_amount ?? 0;
                    $discount = $invoice->discount_amount ?? 0;
                    $debitNote = $invoice->debit_note_adjusted_amount ?? 0;
                    $calculatedNetTotal = max(0, ($subTotal + $tax) - ($discount + $debitNote));
                @endphp

                <div class="flex justify-between border-t border-slate-300 pt-2 text-sm font-bold text-slate-900">
                    <span>Grand Total (Net):</span>
                    <span class="font-mono text-rose-600">{{ number_format($calculatedNetTotal, 2) }} ৳</span>
                </div>
            </div>
        </div>

        <!-- Authorization Signatures -->
        <div class="grid grid-cols-3 gap-4 mt-16 pt-6 text-center text-xs text-slate-500">
            <div>
                <div class="border-b border-slate-300 w-32 mx-auto mb-2"></div>
                <p class="font-bold text-slate-700">Prepared By</p>
            </div>
            <div>
                <div class="border-b border-slate-300 w-32 mx-auto mb-2"></div>
                <p class="font-bold text-slate-700">Checked By</p>
            </div>
            <div>
                <div class="border-b border-slate-300 w-32 mx-auto mb-2"></div>
                <p class="font-bold text-slate-700">Authorized Signature</p>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-3 pt-2 text-center">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
                This is a system-generated invoice. No physical signature is required.
            </p>
        </div>

    </div>

</body>
</html>