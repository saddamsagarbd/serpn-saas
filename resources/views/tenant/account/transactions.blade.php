@extends('layouts.tenant')
@section('title','Master Audit Trail & Transactions')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Master Audit Trail & Transactions</h4>
            <p class="text-xs text-slate-400 mt-0.5">Comprehensive historic ledger of all double-entry transaction rows logged inside the ecosystem.</p>
        </div>
    </div>

    <div class="px-6 py-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" placeholder="Search by Voucher Code, Memo or Account ID..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-700">
        <select class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-600 font-semibold">
            <option>All Accounts Ledger</option>
            <option>1010-001 - Petty Cash Counter</option>
            <option>1020-005 - DBBL Bank Account</option>
        </select>
        <input type="date" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3">Date / Voucher No</th>
                    <th class="p-3">Narration / Particulars</th>
                    <th class="p-3">Account Head Breakdown</th>
                    <th class="p-3 text-right">Debit (৳)</th>
                    <th class="p-3 text-right">Credit (৳)</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-slate-50/40 transition">
                        {{-- ভাউচার নো ও তারিখ --}}
                        <td class="p-3 valign-top">
                            <p class="font-bold text-slate-900 font-mono">{{ $voucher->voucher_no }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ date('d M, Y', strtotime($voucher->date)) }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[9px] font-bold uppercase rounded-md {{ $voucher->type === 'income' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                {{ $voucher->type }}
                            </span>
                        </td>
                        
                        {{-- ন্যারেশন --}}
                        <td class="p-3 font-medium text-slate-600 max-w-xs">
                            {{ $voucher->narration ?? 'N/A' }}
                        </td>

                        {{-- ডাবল এন্ট্রি চাইল্ড ব্রেকডাউন (ম্যাজিক পার্ট) --}}
                        <td colspan="3" class="p-0">
                            <table class="w-full border-collapse">
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($voucher->entries as $entry)
                                        <tr>
                                            <td class="p-3 font-bold {{ $entry->debit > 0 ? 'text-slate-800' : 'text-slate-500 pl-6' }}">
                                                {{ $entry->debit > 0 ? '' : '— ' }} {{ $entry->account->name }} 
                                                <span class="text-[10px] font-mono text-slate-400 font-normal">({{ $entry->account->code }})</span>
                                            </td>
                                            <td class="p-3 text-right font-mono font-bold w-32 text-slate-900">
                                                {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-right font-mono font-bold w-32 text-slate-900">
                                                {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-slate-400">No transactions posted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pt-2">
        {{ $vouchers->links() }}
    </div>
</div>
@endsection