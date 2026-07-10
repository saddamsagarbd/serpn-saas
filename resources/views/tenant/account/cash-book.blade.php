@extends('layouts.tenant')
@section('title','Cash Register Box')
@section('content')

<div class="space-y-5 max-w-7xl mx-auto">
    
    {{-- ১. টপ সার্চ এন্ড ডেট ফিল্টার প্যানেল (আপনার থিমের সাথে ম্যাচ করে) --}}
    <form method="GET" action="{{ route('tenant.accounts.cash-book') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs items-end">
        <div class="flex flex-col md:flex-row items-end gap-3 w-full">
        
            <div class="space-y-1 flex-1 w-full">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
            </div>
            <div class="space-y-1 flex-1 w-full">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex items-center justify-center gap-2 shadow-xs group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                    </svg>
                    <span>Filter</span>
                </button>
                <button type="button" onclick="resetCashBookFilter()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>Reset</span>
                </button>
            </div>
        
        </div>
    </form>

    {{-- ২. কোর ২-কলাম লেআউট স্টার্ট --}}
    @if($cashAccount)
        @php
            // প্রাথমিক লুপ চালনার আগেই ক্লোজিং ব্যালেন্স বের করার জন্য প্রি-ক্যালকুলেশন ট্রিক
            $totalIn = $entries->sum('debit');
            $totalOut = $entries->sum('credit');
            $finalVaultValue = $openingBalance + ($totalIn - $totalOut);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- বাম পাশের প্যানেল: Cash Register Box Summary --}}
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Cash Register Box</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Physical liquid assets across counter.</p>
                    </div>
                    <button type="button" onclick="window.print()" class="text-[11px] font-bold text-indigo-600 hover:underline">🖨️ Print</button>
                </div>
                
                {{-- Dynamic Total Safe Pool Value --}}
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60 text-center space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Vault Liquid Value</span>
                    <p class="text-2xl font-black font-mono text-indigo-600">{{ number_format($finalVaultValue, 2) }} ৳</p>
                </div>
                
                {{-- Accounting Meta Breakdown --}}
                <div class="text-xs text-slate-500 space-y-2 font-medium pt-1">
                    <div class="flex justify-between border-b border-slate-100 pb-1.5">
                        <span>Opening Balance:</span> 
                        <span class="font-mono font-bold text-slate-800">{{ number_format($openingBalance, 2) }} ৳</span>
                    </div>
                    <div class="flex justify-between text-emerald-600 border-b border-slate-100 pb-1.5">
                        <span>Total Cash Inflow:</span> 
                        <span class="font-mono font-bold">+ {{ number_format($totalIn, 2) }} ৳</span>
                    </div>
                    <div class="flex justify-between text-rose-600">
                        <span>Total Cash Outflow:</span> 
                        <span class="font-mono font-bold">- {{ number_format($totalOut, 2) }} ৳</span>
                    </div>
                </div>
            </div>

            {{-- ডান পাশের প্যানেল: Cash Ledger Statements Table --}}
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Cash Ledger Statements</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Chronological running ledger system tracking counter liquid adjustments.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="p-3 pl-0 pb-3">Date</th>
                                <th class="p-3 pb-3">Description Context</th>
                                <th class="p-3 pb-3 text-right">Cash In (+)</th>
                                <th class="p-3 pb-3 text-right">Cash Out (-)</th>
                                <th class="p-3 pr-0 pb-3 text-right">Net Balance</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                            
                            {{-- প্রথম রো: Brought Forward Opening Balance --}}
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="py-3.5 pl-0 font-mono text-slate-400">
                                    {{ date('d-m-Y', strtotime(request('from_date', date('Y-m-01')))) }}
                                </td>
                                <td class="py-3.5">
                                    <p class="font-bold text-slate-800">Initial Cash Balance Opening</p>
                                    <span class="text-[9px] text-indigo-500 font-semibold font-mono">SYSTEM-OPEN</span>
                                </td>
                                <td class="py-3.5 text-right font-mono font-semibold text-slate-300">0.00 ৳</td>
                                <td class="py-3.5 text-right font-mono font-semibold text-slate-300">0.00 ৳</td>
                                <td class="py-3.5 pr-0 text-right font-bold font-mono text-slate-900">
                                    {{ number_format($openingBalance, 2) }} ৳
                                </td>
                            </tr>

                            @php 
                                $runningBalance = $openingBalance; 
                            @endphp

                            {{-- দ্বিতীয় পার্ট: ট্রানজেকশন লুপ পোস্টিং --}}
                            @forelse($entries as $entry)
                                @php
                                    // Cash (Asset) account barle Debit (+), komle Credit (-)
                                    $runningBalance += ($entry->debit - $entry->credit);
                                @endphp
                                <tr class="hover:bg-slate-50/40 transition">
                                    <td class="py-3.5 pl-0 font-mono text-slate-500">
                                        {{ date('d-m-Y', strtotime($entry->voucher->date)) }}
                                    </td>
                                    <td class="py-3.5">
                                        <p class="font-bold text-slate-800">{{ $entry->voucher->narration ?? 'Cash Ledger Document' }}</p>
                                        <span class="text-[9px] text-indigo-500 font-semibold font-mono">{{ $entry->voucher->voucher_no }}</span>
                                    </td>
                                    <td class="py-3.5 text-right font-mono font-bold text-emerald-600">
                                        {{ $entry->debit > 0 ? '+ ' . number_format($entry->debit, 2) . ' ৳' : '0.00 ৳' }}
                                    </td>
                                    <td class="py-3.5 text-right font-mono font-bold text-rose-600">
                                        {{ $entry->credit > 0 ? '- ' . number_format($entry->credit, 2) . ' ৳' : '0.00 ৳' }}
                                    </td>
                                    <td class="py-3.5 pr-0 text-right font-bold font-mono text-slate-900">
                                        {{ number_format($runningBalance, 2) }} ৳
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-slate-400 font-medium">No cash flow transaction records logged inside this targeted date filter.</td>
                                endtr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-xs font-semibold">
            ⚠️ Setup Error: 'Cash in Hand' (Code: 1001) control asset ledger key is missing inside Chart of Accounts.
        </div>
    @endif
</div>

{{-- Print Adjustment CSS --}}
<style>
    @media print {
        body * { visibility: hidden; }
        .grid, .grid * { visibility: visible; }
        .grid { position: absolute; left: 0; top: 0; width: 100%; }
        form, button { display: none !important; }
    }
</style>

@endsection
@push('scripts')
<script>
    function resetCashBookFilter() {
        window.location.href = "{{ route('tenant.accounts.cash-book') }}";
    }
</script>
@endpudh