@extends('layouts.tenant')
@section('title','Profit & Loss Statement')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    
    {{-- টপ হেডার প্যানেল (রিসেট ও ফিল্টার সহ ওয়ান-রো লেআউট ম্যাচ করা হয়েছে) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Profit & Loss / Income Statement</h4>
            <p class="text-xs text-slate-400">Statement of Revenue and Operating Expenses for the targeted fiscal run.</p>
        </div>
        <form method="GET" action="{{ route('tenant.accounts.profit-loss') }}" class="flex items-center gap-2">
            <input type="date" name="from_date" value="{{ $fromDate }}" class="px-2 py-1.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
            <input type="date" name="to_date" value="{{ $toDate }}" class="px-2 py-1.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-600">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition">Filter</button>
        </form>
    </div>

    <div class="max-w-2xl mx-auto space-y-6 text-xs">
        
        {{-- ১. রেভিনিউ সেকশন --}}
        <div class="space-y-2">
            <div class="flex justify-between items-center border-b border-slate-800 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>Operating Revenues</span>
                <span>Amount (BDT)</span>
            </div>
            @forelse($incomeData as $inc)
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>{{ $inc['name'] }}</span>
                    <span class="font-mono font-bold">{{ number_format($inc['amount'], 2) }} ৳</span>
                </div>
            @empty
                <div class="text-slate-400 pl-2 italic">No income revenue streaming recorded inside this frame.</div>
            @endforelse
            <div class="flex justify-between font-bold text-slate-900 pt-1.5 border-t border-slate-200/60 bg-slate-50/50 px-2 py-1 rounded-lg">
                <span>Total Revenue (A)</span>
                <span class="font-mono text-emerald-600">{{ number_format($totalIncome, 2) }} ৳</span>
            </div>
        </div>

        {{-- ২. এক্সপেন্স সেকশন --}}
        <div class="space-y-2">
            <div class="flex justify-between items-center border-b border-slate-800 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>Operating Expenses</span>
                <span></span>
            </div>
            @forelse($expenseData as $exp)
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>{{ $exp['name'] }}</span>
                    <span class="font-mono font-bold">{{ number_format($exp['amount'], 2) }} ৳</span>
                </div>
            @empty
                <div class="text-slate-400 pl-2 italic">No operational overhead expenditures recorded inside this frame.</div>
            @endforelse
            <div class="flex justify-between font-bold text-slate-900 pt-1.5 border-t border-slate-200/60 bg-slate-50/50 px-2 py-1 rounded-lg">
                <span>Total Operating Expense (B)</span>
                <span class="font-mono text-rose-600">{{ number_format($totalExpense, 2) }} ৳</span>
            </div>
        </div>

        {{-- ৩. ফাইনাল রেইটেইন্ড মার্জিন বটম লাইন --}}
        <div class="p-4 rounded-2xl border {{ $netProfitOrLoss >= 0 ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200' }} flex justify-between items-center text-sm font-black text-slate-900">
            <span class="uppercase tracking-wide text-xs {{ $netProfitOrLoss >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $netProfitOrLoss >= 0 ? 'Net Retained Profit / Net Margin Position (A - B)' : 'Net Operating Accumulated Loss (A - B)' }}
            </span>
            <span class="font-mono text-lg {{ $netProfitOrLoss >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                {{ number_format(abs($netProfitOrLoss), 2) }} ৳
            </span>
        </div>

    </div>
</div>
@endsection