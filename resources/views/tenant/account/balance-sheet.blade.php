@extends('layouts.tenant')
@section('title','Balance Sheet')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    <div class="text-center space-y-1 border-b border-slate-100 pb-4">
        <h4 class="text-base font-bold text-slate-900">Statement of Financial Position / Balance Sheet</h4>
        <p class="text-xs text-slate-400">As of current date logging assets holdings alongside offsetting liabilities stakes.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto text-xs">
        
        <div class="space-y-4">
            <div class="flex justify-between items-center border-b-2 border-slate-900 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>1. Company Assets Vault</span>
                <span>Value (BDT)</span>
            </div>
            <div class="space-y-1.5">
                <p class="font-bold text-slate-400 text-[10px] uppercase tracking-tight">Current Assets</p>
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>Petty Cash Desk Drawer Counter</span>
                    <span class="font-mono font-bold">78,500.00 ৳</span>
                </div>
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>Dutch-Bangla Bank Corporate Clearing</span>
                    <span class="font-mono font-bold">1,240,500.00 ৳</span>
                </div>
            </div>
            <div class="flex justify-between font-black text-slate-900 pt-2 border-t-2 border-dashed border-slate-200 bg-slate-50 px-2 py-1.5 rounded-lg text-xs">
                <span>TOTAL ASSETS OWNED</span>
                <span class="font-mono text-indigo-600">1,319,000.00 ৳</span>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center border-b-2 border-slate-900 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>2. Liabilities & Equities Equities</span>
                <span>Value (BDT)</span>
            </div>
            <div class="space-y-1.5">
                <p class="font-bold text-slate-400 text-[10px] uppercase tracking-tight">Equity & Retained Holdings</p>
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>Owner's Core Seed Capital Capital</span>
                    <span class="font-mono font-bold">1,191,500.00 ৳</span>
                </div>
                <div class="flex justify-between text-slate-700 pl-2 font-medium">
                    <span>Retained Earnings Net (P&L Current)</span>
                    <span class="font-mono font-bold">127,500.00 ৳</span>
                </div>
            </div>
            <div class="flex justify-between font-black text-slate-900 pt-2 border-t-2 border-dashed border-slate-200 bg-slate-50 px-2 py-1.5 rounded-lg text-xs">
                <span>TOTAL LIABILITIES & EQUITY MARGINS</span>
                <span class="font-mono text-indigo-600">1,319,000.00 ৳</span>
            </div>
        </div>

    </div>
</div>
@endsection