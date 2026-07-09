@extends('layouts.tenant')
@section('title','Journal Entry')
@section('content')
<div x-data="{ 
    lines: [
        { account_id: '', description: '', debit: 0, credit: 0 },
        { account_id: '', description: '', debit: 0, credit: 0 }
    ],
    addLine() {
        this.lines.push({ account_id: '', description: '', debit: 0, credit: 0 });
    },
    removeLine(index) {
        if(this.lines.length > 2) this.lines.splice(index, 1);
    },
    get totalDebit() {
        return this.lines.reduce((sum, line) => sum + (parseFloat(line.debit) || 0), 0);
    },
    get totalCredit() {
        return this.lines.reduce((sum, line) => sum + (parseFloat(line.credit) || 0), 0);
    },
    get isBalanced() {
        return this.totalDebit > 0 && this.totalDebit === this.totalCredit;
    }
}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Manual Journal Entry (JV)</h4>
            <p class="text-xs text-slate-400 mt-0.5">Record double-entry manual accounting logs. Total debits and credits must equalize.</p>
        </div>
        <div class="text-xs font-mono font-bold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/40">
            Voucher: JV-{{ date('Ymd') }}-AUTO
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/50 rounded-xl border border-slate-200/60">
        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Journal Date *</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700 font-semibold">
        </div>
        <div class="space-y-1.5 md:col-span-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Master Narration / Reference Memo *</label>
            <input type="text" placeholder="Explain the context of this manual adjustment entry..." class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-3 pl-0 pb-3 w-4/12">Account Ledger</th>
                    <th class="p-3 pb-3 w-4/12">Line Description</th>
                    <th class="p-3 pb-3 w-2/12 text-right">Debit (BDT)</th>
                    <th class="p-3 pb-3 w-2/12 text-right">Credit (BDT)</th>
                    <th class="p-3 pr-0 pb-3 w-1/12 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <template x-for="(line, index) in lines" :key="index">
                    <tr class="hover:bg-slate-50/30 transition">
                        <td class="py-3 pl-0">
                            <select x-model="line.account_id" class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-700">
                                <option value="">Select Account Ledger...</option>
                                <option value="1">101001 - Cash Counter Ledger</option>
                                <option value="2">102005 - DBBL Bank Account</option>
                                <option value="3">401001 - General Sales Revenue</option>
                                <option value="4">501002 - Office Utility Expense</option>
                            </select>
                        </td>
                        <td class="py-3 px-2">
                            <input type="text" x-model="line.description" placeholder="Optional row-specific comment..." class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
                        </td>
                        <td class="py-3 px-2">
                            <input type="number" x-model.number="line.debit" @input="line.credit = 0" placeholder="0.00" class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                        </td>
                        <td class="py-3 px-2">
                            <input type="number" x-model.number="line.credit" @input="line.debit = 0" placeholder="0.00" class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                        </td>
                        <td class="py-3 pr-0 text-center">
                            <button type="button" @click="removeLine(index)" :disabled="lines.length <= 2" :class="lines.length <= 2 ? 'opacity-30 cursor-not-allowed' : 'text-rose-500 hover:bg-rose-50'" class="p-1.5 rounded-lg transition">
                                ✕
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <button type="button" @click="addLine()" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50/50 px-3.5 py-2 rounded-xl transition border border-dashed border-indigo-200">
        + Add Account Row
    </button>

    <div class="pt-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <template x-if="isBalanced">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    ✓ Balance Verified (Double-Entry Match)
                </span>
            </template>
            <template x-if="!isBalanced && totalDebit > 0">
                <span class="px-3 py-1 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    ⚠️ Out of Balance (Difference: <span x-text="Math.abs(totalDebit - totalCredit).toLocaleString() + ' ৳'"></span>)
                </span>
            </template>
        </div>

        <div class="w-full md:w-80 space-y-2.5 text-xs font-medium text-slate-500">
            <div class="flex justify-between">
                <span>Total Debit Side</span>
                <span class="font-mono font-bold text-slate-800" x-text="totalDebit.toLocaleString() + ' ৳'"></span>
            </div>
            <div class="flex justify-between">
                <span>Total Credit Side</span>
                <span class="font-mono font-bold text-slate-800" x-text="totalCredit.toLocaleString() + ' ৳'"></span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-slate-200 text-xs font-bold">
                <button type="button" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl transition">Clear Statement</button>
                <button type="submit" :disabled="!isBalanced" :class="isBalanced ? 'bg-indigo-600 hover:bg-indigo-700 shadow-sm text-white' : 'bg-slate-100 text-slate-400 cursor-not-allowed'" class="px-6 py-2 rounded-xl font-bold transition">
                    Post Journal Entry
                </button>
            </div>
        </div>
    </div>

</div>
@endsection