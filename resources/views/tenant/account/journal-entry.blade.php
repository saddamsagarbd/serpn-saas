@extends('layouts.tenant')
@section('title', 'Journal Entry')
@section('content')

<div x-data="{ 
    formData: {
        date: '{{ date('Y-m-d') }}',
        narration: '',
        lines: [
            { account_id: '', description: '', debit: 0, credit: 0 },
            { account_id: '', description: '', debit: 0, credit: 0 }
        ]
    },
    loading: false,
    addLine() {
        this.formData.lines.push({ account_id: '', description: '', debit: 0, credit: 0 });
    },
    removeLine(index) {
        if(this.formData.lines.length > 2) this.formData.lines.splice(index, 1);
    },
    get totalDebit() {
        return this.formData.lines.reduce((sum, line) => sum + (parseFloat(line.debit) || 0), 0);
    },
    get totalCredit() {
        return this.formData.lines.reduce((sum, line) => sum + (parseFloat(line.credit) || 0), 0);
    },
    get isBalanced() {
        return this.totalDebit > 0 && Math.abs(this.totalDebit - this.totalCredit) < 0.01;
    },
    submitJournal() {
        if (!this.isBalanced) {
            alert('Debit and Credit sides must be balanced before posting.');
            return;
        }

        this.loading = true;
        fetch('{{ route('tenant.accounts.journals.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.formData)
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => { 
            this.loading = false; 
            alert('Server posting failed'); 
        });
    }
}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6 relative">
    
    {{-- লোডিং ওভারলে --}}
    <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10" style="display: none;">
        <span class="animate-spin text-xl">🌀</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h4 class="text-base font-bold text-slate-900">Manual Journal Entry (JV)</h4>
            <p class="text-xs text-slate-400 mt-0.5">Record double-entry manual accounting logs. Total debits and credits must equalize.</p>
        </div>
        <div class="text-xs font-mono font-bold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/40">
            Voucher: JV-AUTO
        </div>
    </div>

    <form @submit.prevent="submitJournal()" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/50 rounded-xl border border-slate-200/60">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Journal Date *</label>
                <input type="date" x-model="formData.date" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-mono text-slate-700 font-semibold">
            </div>
            <div class="space-y-1.5 md:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Master Narration / Reference Memo *</label>
                <input type="text" x-model="formData.narration" required placeholder="Explain the context of this manual adjustment entry..." class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-3 w-4/12">Account Ledger *</th>
                        <th class="p-3 pb-3 w-4/12">Line Description</th>
                        <th class="p-3 pb-3 w-2/12 text-right">Debit (BDT)</th>
                        <th class="p-3 pb-3 w-2/12 text-right">Credit (BDT)</th>
                        <th class="p-3 pr-0 pb-3 w-1/12 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    <template x-for="(line, index) in formData.lines" :key="index">
                        <tr class="hover:bg-slate-50/30 transition">
                            <td class="py-3 pl-0">
                                <select x-model="line.account_id" required class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-700">
                                    <option value="">Select Account Ledger...</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3 px-2">
                                <input type="text" x-model="line.description" placeholder="Optional row comment..." class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" step="0.01" x-model.number="line.debit" @input="if(line.debit > 0) line.credit = 0" placeholder="0.00" class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" step="0.01" x-model.number="line.credit" @input="if(line.credit > 0) line.debit = 0" placeholder="0.00" class="w-full p-2 text-xs bg-white border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                            </td>
                            <td class="py-3 pr-0 text-center">
                                <button type="button" @click="removeLine(index)" :disabled="formData.lines.length <= 2" :class="formData.lines.length <= 2 ? 'opacity-30 cursor-not-allowed' : 'text-rose-500 hover:bg-rose-50'" class="p-1.5 rounded-lg transition">
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
                <template x-if="!isBalanced">
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
                    <button type="button" @click="formData.lines = [{account_id:'', description:'', debit:0, credit:0}, {account_id:'', description:'', debit:0, credit:0}]" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl transition">Clear Statement</button>
                    <button type="submit" :disabled="!isBalanced || loading" :class="isBalanced ? 'bg-indigo-600 hover:bg-indigo-700 shadow-sm text-white cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed'" class="px-6 py-2 rounded-xl font-bold transition">
                        Post Journal Entry
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection