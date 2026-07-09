@extends('layouts.tenant')
@section('title','Income')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    <div>
        <h4 class="text-base font-bold text-slate-900">Direct Income / Receipt Voucher</h4>
        <p class="text-xs text-slate-400 mt-0.5">Record miscellaneous income receipts directly into specific cash pools or bank balances.</p>
    </div>

    <form class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Receipt Date *</label>
                <input type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700 font-semibold">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Credit Account (Income Source) *</label>
                <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
                    <option value="">Choose Income Ledger...</option>
                    <option>4010-001 - Retail Sales Revenue</option>
                    <option>4020-002 - Scrap & Waste Materials Sales</option>
                    <option>4030-005 - Bank Interest Income Received</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Debit Account (Receiving Pool) *</label>
                <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
                    <option value="">Choose Destination Box...</option>
                    <option>1010-001 - Petty Cash Counter</option>
                    <option>1020-005 - DBBL Bank Account</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Receipt Amount (BDT) *</label>
                <input type="number" placeholder="0.00" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono font-bold text-slate-900">
            </div>
            <div class="space-y-1.5 md:col-span-2">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Narration Memo / Received From</label>
                <input type="text" placeholder="Specify sender info and clear transaction reasoning details..." class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
            </div>
        </div>

        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 rounded-xl hover:bg-slate-100 transition">Cancel</button>
            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                Post Receipt Voucher
            </button>
        </div>
    </form>
</div>
@endsection