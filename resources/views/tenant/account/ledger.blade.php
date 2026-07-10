@extends('layouts.tenant')
@section('title','General Ledger Account Book')
@section('content')

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5 max-w-6xl mx-auto">
    
    {{-- ১. হেডার সেকশন --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">General Ledger Account Book</h4>
            <p class="text-xs text-slate-400 mt-0.5">Filter specific account codes to audit chronological running statements and balances.</p>
        </div>
        @if(request('account_id'))
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                    🖨️ Print Statement
                </button>
            </div>
        @endif
    </div>

    {{-- ২. অ্যাডভান্সড ফিল্টার ফর্ম (অ্যাকাউন্ট হেড + ডেট রেঞ্জ) --}}
    <form method="GET" action="{{ route('tenant.accounts.ledger') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-slate-50/40 rounded-xl border border-slate-200/40 items-end">
        <div class="space-y-1 md:col-span-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Ledger *</label>
            <select name="account_id" required class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option value="">-- Select Control Account --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->code }} - {{ $account->name }} [{{ ucfirst($account->type) }}]
                    </option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
        </div>
        <div>
            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-xs group">
                {{-- 💡 প্রিমিয়াম মিনিমালিস্ট সার্চ SVG আইকন --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                </svg>
                <span>Load Ledger</span>
            </button>
        </div>
    </form>

    {{-- ৩. লেজার রেকর্ড টেবিল --}}
    @if(request('account_id'))
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-3">Date</th>
                        <th class="p-3 pb-3">Voucher Ref</th>
                        <th class="p-3 pb-3">Narration Description</th>
                        <th class="p-3 pb-3 text-right">Debit (DR)</th>
                        <th class="p-3 pb-3 text-right">Credit (CR)</th>
                        <th class="p-3 pr-0 pb-3 text-right">Current Balance</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    
                    {{-- ১. প্রারম্ভিক উদ্বৃত্ত / Opening Balance Row --}}
                    <tr class="bg-slate-50/50">
                        <td class="py-3 pl-0 text-slate-400 font-mono">{{ date('d-m-Y', strtotime(request('from_date', date('Y-m-01')))) }}</td>
                        <td class="py-3 font-mono text-slate-400">-</td>
                        <td class="py-3 font-bold text-slate-500 italic">Statement Opening Balance Brought Forward</td>
                        <td class="py-3 text-right font-mono text-slate-300">0.00 ৳</td>
                        <td class="py-3 text-right font-mono text-slate-300">0.00 ৳</td>
                        <td class="py-3 pr-0 text-right font-bold font-mono text-slate-800">
                            {{ number_format($openingBalance, 2) }} ৳
                        </td>
                    </tr>

                    @php 
                        $runningBalance = $openingBalance; 
                        $selectedAcc = $accounts->firstWhere('id', request('account_id'));
                        $accountType = $selectedAcc ? $selectedAcc->type : 'asset';
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp

                    {{-- ২. ডাইনামিক ট্রানজেকশন লুপ --}}
                    @forelse($entries as $entry)
                        @php
                            $totalDebit += $entry->debit;
                            $totalCredit += $entry->credit;

                            // 💡 কোর অ্যাকাউন্টিং রানিং ব্যালেন্স ফর্মুলা:
                            // Assets & Expenses বাড়ে ডেবিটে (+), কমে ক্রেডিটে (-)
                            // Liabilities, Equity, Income বাড়ে ক্রেডিটে (+), কমে ডেবিটে (-)
                            if(in_array($accountType, ['asset', 'expense'])) {
                                $runningBalance += ($entry->debit - $entry->credit);
                            } else {
                                $runningBalance += ($entry->credit - $entry->debit);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="py-3 pl-0 text-slate-500 font-mono">
                                {{ date('d-m-Y', strtotime($entry->voucher->date)) }}
                            </td>
                            <td class="py-3 font-mono font-bold text-indigo-600">
                                {{ $entry->voucher->voucher_no }}
                            </td>
                            <td class="py-3 font-medium text-slate-700 max-w-xs">
                                {{ $entry->voucher->narration ?? 'N/A' }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-emerald-600">
                                {{ $entry->debit > 0 ? '+ ' . number_format($entry->debit, 2) . ' ৳' : '0.00 ৳' }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-rose-600">
                                {{ $entry->credit > 0 ? '- ' . number_format($entry->credit, 2) . ' ৳' : '0.00 ৳' }}
                            </td>
                            <td class="py-3 pr-0 text-right font-bold font-mono text-slate-900">
                                {{ number_format($runningBalance, 2) }} ৳
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-400 font-medium">No ledger movement records found for this selected period range.</td>
                        </tr>
                    @endforelse

                    {{-- ৩. পিরিয়ডিকাল ক্লোজিং টোটাল সমীকরণ --}}
                    @if(count($entries) > 0)
                        <tr class="bg-slate-50/80 font-black text-slate-900 border-t-2 border-slate-200">
                            <td colspan="3" class="p-3 pl-0 text-right uppercase tracking-wider text-[10px] text-slate-500">Total Period Summary:</td>
                            <td class="p-3 text-right font-mono text-emerald-700">{{ number_format($totalDebit, 2) }} ৳</td>
                            <td class="p-3 text-right font-mono text-rose-700">{{ number_format($totalCredit, 2) }} ৳</td>
                            <td class="p-3 pr-0 text-right font-mono text-indigo-600 bg-indigo-50/40 rounded-r-xl">{{ number_format($runningBalance, 2) }} ৳</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @else
        {{-- কোনো অ্যাকাউন্ট সিলেক্ট না থাকলে নোটিফিকেশন স্ক্রিন --}}
        <div class="bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 p-12 text-center">
            <span class="text-3xl block mb-2">📊</span>
            <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide">No Ledger Requested</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">Please select an account head and define your date filter parameters above to compute running trial statement balances.</p>
        </div>
    @endif
</div>

{{-- প্রিন্ট করার সিএসএস ওভাররাইড রুল --}}
<style>
    @media print {
        body * { visibility: hidden; }
        .max-w-6xl, .max-w-6xl * { visibility: visible; }
        .max-w-6xl { position: absolute; left: 0; top: 0; width: 100%; border: none; padding: 0; }
        form, button { display: none !important; }
    }
</style>

@endsection