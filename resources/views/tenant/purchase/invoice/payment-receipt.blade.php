<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6 text-gray-800 font-sans">

    <!-- Print / Close Bar -->
    <div class="max-w-3xl mx-auto mb-4 flex justify-between items-center no-print">
        <a href="{{ route('tenant.purchase.invoice.payment') }}" class="text-xs font-bold text-gray-600 hover:text-gray-800">
            &larr; Back to Payments List
        </a>
        <button onclick="window.print()" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition">
            Print / Save PDF
        </button>
    </div>

    <!-- Main Receipt Card -->
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        
        <!-- Header Section -->
        <div class="flex justify-between items-start border-b border-gray-100 pb-6">
            <div>
                <h1 class="text-2xl font-black text-rose-600 tracking-tight">PAYMENT RECEIPT</h1>
                <p class="text-xs font-mono text-gray-400 mt-1">Receipt No: <span class="text-gray-800 font-bold">{{ $payment->payment_no }}</span></p>
                <p class="text-xs font-mono text-gray-400">Date: <span class="text-gray-800">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</span></p>
            </div>
            <div class="text-right">
                <h2 class="text-base font-bold text-gray-800">{{ tenant('company_name') ?? 'Your Company Name' }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manufacturing & ERP Division</p>
                <p class="text-xs text-gray-400">Voucher Ref: {{ $payment->voucher?->voucher_no ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Supplier & Payment Meta Details -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Paid To (Vendor)</span>
                <p class="font-bold text-gray-900 text-sm">{{ $payment->supplier?->name }}</p>
                <p class="text-gray-600 mt-0.5">{{ $payment->supplier?->address ?? 'No address provided' }}</p>
                <p class="text-gray-600">Phone: {{ $payment->supplier?->phone ?? 'N/A' }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Payment Method & Info</span>
                <p class="text-gray-700"><span class="font-bold">Method:</span> <span class="capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span></p>
                <p class="text-gray-700"><span class="font-bold">Paid From Account:</span> {{ $payment->bankAccount?->name ?? 'Bank/Cash' }}</p>
                <p class="text-gray-700"><span class="font-bold">Ref / Cheque No:</span> {{ $payment->reference_no ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Invoice Allocation Breakdown Table -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Invoice Settlement Breakdown (FIFO)</h3>
            <div class="border border-gray-200 rounded-xl overflow-hidden text-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4">Invoice No</th>
                            <th class="p-3">Invoice Date</th>
                            <th class="p-3 text-right">Settled / Allocated Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($payment->items as $item)
                            <tr>
                                <td class="p-3 pl-4 font-bold text-rose-600">{{ $item->invoice?->invoice_no }}</td>
                                <td class="p-3 text-gray-500">{{ \Carbon\Carbon::parse($item->invoice?->invoice_date)->format('d M, Y') }}</td>
                                <td class="p-3 text-right font-mono font-bold text-gray-800">{{ number_format($item->allocated_amount, 2) }} ৳</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-400 font-medium">
                                    No specific invoice allocated (Direct Advance Payment).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Box for Pure Advance -->
        <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 text-xs space-y-2">
            <div class="flex justify-between text-gray-600">
                <span>Total Cash / Bank Disbursed:</span>
                <span class="font-mono font-bold text-gray-800">{{ number_format($payment->total_paid_amount, 2) }} ৳</span>
            </div>
            
            @if(($payment->advance_amount ?? 0) > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Paid as Advance:</span>
                    <span class="font-mono font-bold text-emerald-600">{{ number_format($payment->advance_amount, 2) }} ৳</span>
                </div>
            @endif
        </div>

        <!-- Footer / Signature Blocks -->
        <div class="pt-12 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <div class="border-t border-gray-300 w-36 mx-auto mb-1"></div>
                <p class="font-bold text-gray-700">Accounts Department</p>
                <p class="text-[10px] text-gray-400">Authorized Signature</p>
            </div>
            <div>
                <div class="border-t border-gray-300 w-36 mx-auto mb-1"></div>
                <p class="font-bold text-gray-700">Supplier Receiver</p>
                <p class="text-[10px] text-gray-400">Received With Thanks</p>
            </div>
        </div>

    </div>

</body>
</html>