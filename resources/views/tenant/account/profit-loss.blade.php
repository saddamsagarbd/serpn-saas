@extends('layouts.tenant')
@section('title','Trial Balance Worksheet')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    <div class="text-center space-y-1 border-b border-slate-100 pb-4">
        <h4 class="text-base font-bold text-slate-900">Profit & Loss / Income Statement</h4>
        <p class="text-xs text-slate-400">Statement of Revenue and Operating Expenses for the ongoing fiscal run.</p>
    </div>

    <div class="max-w-2xl mx-auto space-y-6 text-xs">
        
        <div class="space-y-2">
            <div class="flex justify-between items-center border-b border-slate-800 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>Operating Revenues</span>
                <span>Amount (BDT)</span>
            </div>
            <div class="flex justify-between text-slate-700 pl-2 font-medium">
                <span>Gross ERP POS Retail Sales Revenue</span>
                <span class="font-mono font-bold">145,000.00 ৳</span>
            </div>
            <div class="flex justify-between text-slate-700 pl-2 font-medium">
                <span>Service Rendering Fees & Subscriptions</span>
                <span class="font-mono font-bold">12,000.00 ৳</span>
            </div>
            <div class="flex justify-between font-bold text-slate-900 pt-1.5 border-t border-slate-200/60 bg-slate-50/50 px-2 py-1 rounded-lg">
                <span>Total Revenue (A)</span>
                <span class="font-mono text-emerald-600">157,000.00 ৳</span>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex justify-between items-center border-b border-slate-800 pb-1.5 font-black text-slate-900 uppercase tracking-wider text-[11px]">
                <span>Operating Expenses</span>
                <span></span>
            </div>
            <div class="flex justify-between text-slate-700 pl-2 font-medium">
                <span>Office Showroom Space Rental Cost</span>
                <span class="font-mono font-bold">25,000.00 ৳</span>
            </div>
            <div class="flex justify-between text-slate-700 pl-2 font-medium">
                <span>Electricity & Broadband Infrastructure Utilities</span>
                <span class="font-mono font-bold">4,500.00 ৳</span>
            </div>
            <div class="flex justify-between font-bold text-slate-900 pt-1.5 border-t border-slate-200/60 bg-slate-50/50 px-2 py-1 rounded-lg">
                <span>Total Operating Expense (B)</span>
                <span class="font-mono text-rose-600">29,500.00 ৳</span>
            </div>
        </div>

        <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 flex justify-between items-center text-sm font-black text-slate-900">
            <span class="uppercase tracking-wide text-xs text-indigo-700">Net Retained Profit / Net Margin Position (A - B)</span>
            <span class="font-mono text-indigo-600 text-lg">127,500.00 ৳</span>
        </div>

    </div>
</div>
@endsection