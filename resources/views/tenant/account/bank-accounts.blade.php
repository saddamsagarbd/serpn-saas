@extends('layouts.tenant')
@section('title','Corporate Bank Accounts')
@section('content')
<div x-data="{ openConnectModal: false }">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h4 class="text-base font-bold text-slate-900">Corporate Bank Accounts</h4>
                <p class="text-xs text-slate-400 mt-0.5">Configure internal bank accounts and view linked real-time bank ledger clearings.</p>
            </div>
            <button type="button" @click="openConnectModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-xs transition">
                + Connect Bank Account
            </button>
        </div>

        {{-- ব্যাংক কার্ড গ্রিড --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($bankAccounts as $bank)
                @php
                    // ব্যাংক খতিয়ানের ডেবিট (জমা) এবং ক্রেডিট (উত্তোলন) হিসেব করে কারেন্ট ব্যালেন্স বের করা
                    $ledgerEntries = $bank->ledgerEntries ?? collect([]);

                    $debitTotal = $ledgerEntries->sum('debit') ?? 0;
                    $creditTotal = $ledgerEntries->sum('credit') ?? 0;

                    // ব্যাংক (Asset Head) এর ব্যালেন্স = ওপেনিং + ডেবিট - ক্রেডিট
                    $currentBalance = $bank->opening_balance + ($debitTotal - $creditTotal);
                @endphp
                <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/30 flex justify-between items-start hover:border-indigo-500 transition group cursor-pointer" onclick="window.location.href='{{ route('tenant.accounts.ledger', ['account_id' => $bank->id]) }}'">
                    <div class="space-y-1">
                        <span class="text-[9px] font-mono bg-indigo-50 text-indigo-600 font-bold px-1.5 py-0.5 rounded">A/C {{ $bank->account_number ?? $bank->code }}</span>
                        <h5 class="text-xs font-bold text-slate-800 pt-1 group-hover:text-indigo-600 transition-colors">{{ $bank->name }}</h5>
                        <p class="text-[10px] text-slate-400 font-medium">Branch: {{ $bank->branch_name ?? 'Corporate Head Branch' }}</p>
                    </div>
                    <div class="text-right space-y-1.5">
                        <span class="text-[10px] text-slate-400 block font-medium">Available Clear Balance</span>
                        <span class="font-mono font-black text-slate-900 text-sm">{{ number_format($currentBalance, 2) }} ৳</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center p-6 text-slate-400 text-xs font-medium">
                    No active corporate bank accounts linked in the chart of accounts.
                </div>
            @endforelse
        </div>
    </div>

    {{-- 🏦 ব্যাংক অ্যাকাউন্ট কানেক্ট করার মডাল ব্যাকড্রপ (Pop-up Modal Overlay) --}}
    <div x-show="openConnectModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
         
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            {{-- ব্যাকড্রপ শ্যাডো --}}
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-xs" @click="openConnectModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            {{-- মডাল বডি কার্ড --}}
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <form method="POST" action="{{ route('tenant.accounts.coa.store') }}">
                    @csrf
                    {{-- ব্যাকএন্ডে সাবমিটের সুবিধার্থে অ্যাকাউন্ট টাইপ হাইড রাখা হলো --}}
                    <input type="hidden" name="type" value="asset">

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">🏦 Connect Corporate Bank</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Add a new ledger under primary assets configuration node.</p>
                            </div>
                            <button type="button" @click="openConnectModal = false" class="text-slate-400 hover:text-slate-600 text-xs font-bold">✕</button>
                        </div>

                        {{-- অ্যাকাউন্ট কোড ইনপুট --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ledger Code (Must start with 1020) *</label>
                            <input type="text" name="code" required placeholder="e.g., 1020-006" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700">
                        </div>

                        {{-- ব্যাংক নেম ও অ্যাকাউন্ট নম্বর --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bank Label Name & A/C Number *</label>
                            <input type="text" name="name" required placeholder="e.g., BRAC Bank PLC - A/C 1502..." class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                        </div>

                        {{-- ওপেনিং ব্যালেন্স --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Initial Opening Balance (BDT) *</label>
                            <input type="number" step="0.01" name="opening_balance" value="0.00" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700">
                        </div>
                    </div>

                    {{-- মডাল ফুটার একশন বাটন --}}
                    <div class="bg-slate-50 px-6 py-3.5 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="openConnectModal = false" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold px-4 py-2 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-xs">
                            Save & Link Ledger
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection