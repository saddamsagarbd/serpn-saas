@extends('layouts.tenant')
@section('title','Corporate Bank Accounts')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Corporate Bank Accounts</h4>
            <p class="text-xs text-slate-400 mt-0.5">Configure internal bank accounts and view linked real-time bank ledger clearings.</p>
        </div>
        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-xs transition">
            + Connect Bank Account
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/30 flex justify-between items-start hover:border-indigo-500 transition group">
            <div class="space-y-1">
                <span class="text-[9px] font-mono bg-indigo-50 text-indigo-600 font-bold px-1.5 py-0.5 rounded">A/C 124.105.9928</span>
                <h5 class="text-xs font-bold text-slate-800 pt-1 group-hover:text-indigo-600 transition-colors">Dutch-Bangla Bank PLC</h5>
                <p class="text-[10px] text-slate-400 font-medium">Branch: Motijheel Corporate Branch</p>
            </div>
            <div class="text-right space-y-1.5">
                <span class="text-[10px] text-slate-400 block font-medium">Available Clear Balance</span>
                <span class="font-mono font-black text-slate-900 text-sm">1,240,500 ৳</span>
            </div>
        </div>

        <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/30 flex justify-between items-start hover:border-indigo-500 transition group">
            <div class="space-y-1">
                <span class="text-[9px] font-mono bg-indigo-50 text-indigo-600 font-bold px-1.5 py-0.5 rounded">A/C 1502.991.002</span>
                <h5 class="text-xs font-bold text-slate-800 pt-1 group-hover:text-indigo-600 transition-colors">BRAC Bank PLC</h5>
                <p class="text-[10px] text-slate-400 font-medium">Branch: Gulshan Circle-2 Desk</p>
            </div>
            <div class="text-right space-y-1.5">
                <span class="text-[10px] text-slate-400 block font-medium">Available Clear Balance</span>
                <span class="font-mono font-black text-slate-900 text-sm">450,000 ৳</span>
            </div>
        </div>
    </div>
</div>
@endsection