@extends('layouts.tenant')
@section('title', 'Income Voucher Entry')
@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6"
     x-data="{
        formData: {
            type: 'income',
            date: new Date().toISOString().split('T')[0],
            chart_of_account_id: '',
            payment_method_id: '',
            payment_type: 'Final Payment',
            payment_gateway: 'CASH',
            reference_no: '',
            party_name: '',
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
                    window.location.href = `{{ route('tenant.accounts.income.list') }}`
                    {{-- ফর্ম রিসেট --}}
                    this.formData.chart_of_account_id = '';
                    this.formData.payment_method_id = '';
                    this.formData.payment_type = 'Final Payment';
                    this.formData.payment_gateway = 'CASH';
                    this.formData.reference_no = '';
                    this.formData.party_name = '';
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
        <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">🟢 Direct Income Voucher Entry</h2>
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
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Received Amount (৳) *</label>
                <input type="number" step="0.01" x-model="formData.amount" required placeholder="0.00" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-bold font-mono text-slate-800">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">

            {{-- বায়ার / ক্লায়েন্ট নাম --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Buyer / Client Name</label>
                <input type="text" x-model="formData.party_name" placeholder="e.g., Company X" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">

            {{-- ইনকাম লেজার সিলেক্ট --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Income Account Head *</label>
                <select x-model="formData.chart_of_account_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                    <option value="">-- Choose Revenue Sector --</option>
                    @foreach($incomeHeads as $head)
                        <option value="{{ $head->id }}">{{ $head->name }} ({{ $head->code }})</option>
                    @endforeach
                </select>
            </div>

            {{-- ডিপোজিট অ্যাকাউন্ট (Cash Book / Bank / MFS) --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deposit To (Cash Book / Bank Account) *</label>
                <select x-model="formData.payment_method_id" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                    <option value="">-- Choose Asset Method --</option>
                    @foreach($assetHeads as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- পেমেন্ট টাইপ এবং গেটওয়ে --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment Type *</label>
                <select x-model="formData.payment_type" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
                    <option value="">-- Choose payment type --</option>
                    <option value="advance">Advance Payment</option>
                    <option value="partial">Partial Payment</option>
                    <option value="final">Final / Closing Payment</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Receiving Gateway *</label>
                <select x-model="formData.payment_gateway" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700">
                    <option value="">-- Choose gateway --</option>
                    <option value="npsb">NPSB</option>
                    <option value="rtgs">RTGS</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="rocket">Rocket</option>
                </select>
            </div>
        </div>

        {{-- রেফারেন্স বা চেক নম্বর --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reference / Cheque No / Trx ID</label>
            <input type="text" x-model="formData.reference_no" placeholder="e.g., TrxID or Cheque No" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 font-mono">
        </div>

        {{-- ন্যারেশন বা নোট --}}
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Narration / Remarks</label>
            <textarea x-model="formData.narration" rows="3" placeholder="Write specific details about this income..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium"></textarea>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" :disabled="loading" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                <span x-show="loading" class="animate-spin text-xs">🌀</span>
                Post Income Ledger
            </button>
        </div>
    </form>
</div>

@endsection