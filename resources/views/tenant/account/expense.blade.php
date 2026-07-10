@extends('layouts.tenant')
@section('title', 'Expense Voucher Entry')
@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6"
     x-data="{
        formData: {
            type: 'expense',
            date: new Date().toISOString().split('T')[0],
            chart_of_account_id: '',
            payment_method_id: '',
            amount: '',
            narration: ''
        },
        loading: false,
        submitVoucher() {
            this.loading = true;
            fetch('{{ route('tenant.accounts.vouchers.store') }}', {
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
                    {{-- ফর্ম রিসেট --}}
                    this.formData.chart_of_account_id = '';
                    this.formData.amount = '';
                    this.formData.narration = '';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => { this.loading = false; alert('Server posting failed'); });
        }
     }">

    <div>
        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">🟢 Direct Expense Voucher Entry</h2>
        <p class="text-xs text-slate-400 mt-0.5">Post real-time cash flow revenue into core ledger stream.</p>
    </div>

    <form @submit.prevent="submitVoucher()" class="space-y-4 pt-2">
        <div class="grid grid-cols-2 gap-4">
            {{-- পোস্টিং ডেট --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Voucher Date *</label>
                <input type="date" x-model="formData.date" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
            </div>
            {{-- অ্যামাউন্ট --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paid Amount (৳) *</label>
                <input type="number" step="0.01" x-model="formData.amount" required placeholder="0.00" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-bold font-mono text-slate-800">
            </div>
        </div>

        {{-- ইনকাম লেজার সিলেক্ট --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expense Account Head *</label>
            <select x-model="formData.chart_of_account_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                <option value="">-- Choose Revenue Sector --</option>
                @foreach($expenseHeads as $head)
                    <option value="{{ $head->id }}">{{ $head->name }} ({{ $head->code }})</option>
                @endforeach
            </select>
        </div>

        {{-- ক্যাশ বা ব্যাংক লেজার সিলেক্ট (Debit Side) --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deposit To (Cash Book / Bank Account) *</label>
            <select x-model="formData.payment_method_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                <option value="">-- Choose Asset Method --</option>
                @foreach($assetHeads as $asset)
                    <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }})</option>
                @endforeach
            </select>
        </div>

        {{-- ন্যারেশন বা নোট --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Narration / Remarks</label>
            <textarea x-model="formData.narration" rows="3" placeholder="Write specific details about this income..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium"></textarea>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" :disabled="loading" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                <span x-show="loading" class="animate-spin text-xs">🌀</span>
                Post Expense Ledger
            </button>
        </div>
    </form>
</div>

@endsection