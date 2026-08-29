@extends('layouts.tenant')
@section('title', 'Purchase Returns & Debit Notes')

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
        <a href="{{ route('tenant.purchase.return.create') }}" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm transition">
            + Create New Purchase Return
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b border-slate-200">
                    <th class="p-3 pl-6">Return No / Date</th>
                    <th class="p-3">GRN Ref</th>
                    <th class="p-3">Supplier Name</th>
                    <th class="p-3">Reason</th>
                    <th class="p-3 text-right">Total Returned Value</th>
                    <th class="p-3 pr-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                @forelse($returns as $return)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-3 pl-6">
                            <span class="font-mono font-bold text-rose-600 block">{{ $return->return_no }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($return->return_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $return->goodsReceivedNote->grn_no ?? 'N/A' }}
                        </td>
                        <td class="p-3 font-bold text-slate-800">
                            {{ $return->supplier->name ?? 'N/A' }}
                        </td>
                        <td class="p-3 text-slate-500 italic">
                            {{ Str::limit($return->reason, 30) ?: 'N/A' }}
                        </td>
                        <td class="p-3 text-right font-mono font-bold text-rose-600">
                            {{ number_format($return->total_amount, 2) }} ৳
                        </td>
                        <td class="p-3 pr-6 text-center">
                            <a href="{{ route('tenant.purchase.return.details', ['id' => $return->id]) }}" class="px-2.5 py-1 text-[11px] font-bold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                                View Debit Note
                            </a>
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