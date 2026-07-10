@extends('layouts.tenant')
@section('title','Trial Balance Worksheet')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Trial Balance Worksheet</h4>
            <p class="text-xs text-slate-400 mt-0.5">Comprehensive audit matrix testing if global debits perfectly balance global credits.</p>
        </div>
        <span class="text-xs font-mono font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200/50">
            ✓ Ledger Status: {{ number_format($totalDebitSum, 2) == number_format($totalCreditSum, 2) ? 'Balanced' : 'Unbalanced' }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-4">Account Code</th>
                    <th class="p-3">Ledger Name Group</th>
                    <th class="p-3 text-center">Account Class</th>
                    <th class="p-3 text-right">Debit Balance</th>
                    <th class="p-3 pr-4 text-right">Credit Balance</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                @forelse($trialBalanceData as $row)
                    <tr class="hover:bg-slate-50/30 transition">
                        <td class="p-3 pl-4 font-mono font-bold text-slate-400">{{ $row['code'] }}</td>
                        <td class="p-3 font-bold text-slate-800">{{ $row['name'] }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 text-[9px] font-bold rounded {{ $row['type'] == 'income' || $row['type'] == 'liability' ? 'bg-indigo-50 text-indigo-500' : 'bg-slate-100 text-slate-500' }} capitalize">
                                {{ $row['type'] }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-mono font-bold text-slate-900">
                            {{ $row['debit'] > 0 ? number_format($row['debit'], 2) . ' ৳' : '0.00 ৳' }}
                        </td>
                        <td class="p-3 pr-4 text-right font-mono font-bold text-slate-900">
                            {{ $row['credit'] > 0 ? number_format($row['credit'], 2) . ' ৳' : '0.00 ৳' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-slate-400 font-medium">No account heads computed inside the system yet.</td>
                    </tr>
                @endforelse

                {{-- গ্র্যান্ড সামারি টোটাল রো --}}
                <tr class="bg-slate-50/80 font-black text-slate-900 border-t border-slate-300">
                    <td class="p-3.5 pl-4 uppercase tracking-wider text-[10px]" colspan="3">Grand Summary Total equalization</td>
                    <td class="p-3.5 text-right font-mono text-indigo-600 text-sm">{{ number_format($totalDebitSum, 2) }} ৳</td>
                    <td class="p-3.5 pr-4 text-right font-mono text-indigo-600 text-sm">{{ number_format($totalCreditSum, 2) }} ৳</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection