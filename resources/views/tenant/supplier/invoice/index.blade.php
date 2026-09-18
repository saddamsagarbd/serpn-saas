@extends('layouts.tenant')

@section('content')
<div class="p-6">
    <!-- Header Area -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Supplier Invoices (Bills)</h1>
            <p class="text-sm text-slate-500">Manage vendor bills, payment statuses, and 3-way matching records.</p>
        </div>
        <a href="{{ route('tenant.purchase.invoice.create') }}" class="px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-bold text-sm shadow-md transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Invoice
        </a>
    </div>

    <!-- DataTable Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <div class="overflow-x-auto">
            <table id="supplierInvoicesTable" class="w-full text-left text-sm display border-collapse">
                <thead class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="p-3">Invoice Details</th>
                        <th class="p-3">Supplier</th>
                        <th class="p-3">Ref GRN / PO</th>
                        <th class="p-3 text-center">Dates</th>
                        <th class="p-3 text-right">Grand Total</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Invoice No & Voucher -->
                            <td class="p-3 font-medium text-slate-800">
                                <span class="block font-bold text-purple-900">{{ $invoice->invoice_no }}</span>
                                <span class="text-xs text-slate-400">Voucher: {{ $invoice->voucher->voucher_no ?? 'N/A' }}</span>
                            </td>

                            <!-- Supplier Name -->
                            <td class="p-3 text-slate-700">
                                <span class="font-semibold block">{{ $invoice->supplier->name ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-400">{{ $invoice->supplier->code ?? '' }}</span>
                            </td>

                            <!-- References -->
                            <td class="p-3 text-slate-600 text-xs">
                                <span class="block"><strong>GRN:</strong> {{ $invoice->grn->grn_no ?? 'N/A' }}</span>
                                <span class="block text-slate-400"><strong>PO:</strong> {{ $invoice->purchaseOrder->po_no ?? 'N/A' }}</span>
                            </td>

                            <!-- Dates -->
                            <td class="p-3 text-center text-xs">
                                <span class="block font-medium text-slate-700">Inv: {{ $invoice->invoice_date }}</span>
                                <span class="block text-red-500 font-semibold">Due: {{ $invoice->due_date }}</span>
                            </td>

                            <!-- Grand Total -->
                            <td class="p-3 text-right font-bold text-slate-900">
                                {{ number_format($invoice->grand_total, 2) }} ৳
                            </td>

                            <!-- Status Badge -->
                            <td class="p-3 text-center">
                                @if($invoice->status === 'paid')
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase">Paid</span>
                                @elseif($invoice->status === 'partially_paid')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase">Partial</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold uppercase">Unpaid</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('tenant.purchase.invoices.show', $invoice->id) }}" title="View Invoice" class="p-1.5 text-slate-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    @if($invoice->status !== 'paid')
                                        <a href="#" title="Make Payment" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
@push('scripts')
<script>
    $(document).ready(function() {
        $('#supplierInvoicesTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search Invoices...",
            }
        });
    });
</script>
@endpush
@endsection