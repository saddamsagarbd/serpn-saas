@extends('layouts.tenant')
@section('title', 'Supplier Invoice')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-slate-800">Purchase Returns / Debit Notes</h3>
            <p class="text-xs text-slate-400">List of supplier returns, reduced inventory & accounts ledger debit notes.</p>
        </div>
        <a href="{{ route('tenant.purchase.suppliers.invoice.create') }}" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm transition">
            + Create New Purchase Return
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b border-slate-200">
                    <th class="p-3 pl-6">Invoice No</th>
                    <th class="p-3">Supplier</th>
                    <th class="p-3">GRN Ref</th>
                    <th class="p-3 text-right">Net Amount</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 pr-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-3 pl-6">                            
                            <span class="font-mono font-bold text-rose-600 block">{{ $invoice->invoice_no }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $invoice->supplier->name }}
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $return->goodsReceivedNote->grn_no ?? 'N/A' }}
                        </td>
                        <td class="p-3 text-right font-mono font-bold text-rose-600">
                            {{ number_format($invoice->net_amount, 2) }} ৳
                        </td>
                        <td>
                            <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('tenant.supplier-invoices.edit', $invoice->id) }}" class="btn btn-sm btn-info">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                            No purchase returns or debit notes recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection