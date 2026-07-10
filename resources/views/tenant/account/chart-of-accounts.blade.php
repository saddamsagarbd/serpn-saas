@extends('layouts.tenant')
@section('title','Chart of Accounts (COA)')
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" 
     x-data="{ 
        currentTab: 'income', {{-- ডিফল্ট ট্যাব হিসেবে ইনকাম সেট করা হলো --}}
        openModal: false,
        isEdit: false,
        formAction: '{{ route('tenant.accounts.coa.store') }}',
        
        {{-- একাউন্ট ফর্ম মডেল --}}
        accountData: {
            id: '',
            name: '',
            code: '',
            type: 'income',
            parent_id: '',
            opening_balance: '0.00'
        },
        
        accounts: [],
        loading: false,

        initCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('tenant.accounts.coa.store') }}';
            this.accountData = { id: '', name: '', code: '', type: this.currentTab, parent_id: '', opening_balance: '0.00' };
            this.openModal = true;
        },

        editAccount(account) {
            this.isEdit = true;
            {{-- পরবর্তীতে আপডেট রাউট লাগলে এখানে ক্যাটাগরি/ইউনিটের মতো সেট করতে পারবেন --}}
            let baseUrl = '{{ route("tenant.accounts.coa.update", ["id" => ":id"]) }}';
            this.formAction = baseUrl.replace(':id', account.id);
            this.accountData = {
                id: account.id,
                name: account.name,
                code: account.code,
                type: account.type,
                parent_id: account.parent_id ?? '',
                opening_balance: account.opening_balance ?? '0.00'
            };
            this.openModal = true;
        },

        fetchAccounts() {
            this.loading = true;
            fetch('{{ route('tenant.accounts.coa.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(res => {
                console.log(res.data);
                this.accounts = res.data || [];
                this.loading = false;
            })
            .catch(err => {
                console.error('Error fetching COA:', err);
                this.loading = false;
            });
        },

        saveAccount() {
            const token = document.querySelector('input[name=\'_token\']')?.value;
            let formData = { ...this.accountData };

            fetch(this.formAction, {
                method: this.isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) throw new Error('Validation or Server Error');
                return response.json();
            })
            .then(data => {
                this.openModal = false;
                this.fetchAccounts(); {{-- পেজ রিফ্রেশ ছাড়া রিয়েল-টাইম ডাটা লোড --}}
                if (typeof toastr !== 'undefined') toastr.success(data.message || 'Account created!');
            })
            .catch(err => alert('Failed to save account head. Unique code required.'));
        }
     }" 
     x-init="fetchAccounts()">
    
    {{-- বাম পাশের মেনু বা স্ট্রাকচার উইজেট --}}
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-3">
        <div>
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Accounting Structure</h4>
            <p class="text-xs text-slate-400 mt-0.5">Core account classification trees.</p>
        </div>
        <div class="flex flex-col gap-1 pt-2">
            <button @click="currentTab = 'asset'" :class="currentTab === 'asset' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 1000 - Assets Accounts</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'liability'" :class="currentTab === 'liability' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 2000 - Liabilities</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'equity'" :class="currentTab === 'equity' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 3000 - Owner's Equity</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'income'" :class="currentTab === 'income' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 4000 - Operating Income</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'expense'" :class="currentTab === 'expense' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 5000 - Operating Expenses</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
        </div>
    </div>

    {{-- ডান পাশের লেজার টেবিল উইজেট --}}
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h4 class="text-base font-bold text-slate-900" x-text="currentTab.toUpperCase() + ' Accounts Tree'"></h4>
                <p class="text-xs text-slate-400 mt-0.5">Sub-ledgers assigned under primary classification context block.</p>
            </div>
            <button type="button" @click="initCreate()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                + Create New Ledger Account
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-3">Ledger Code</th>
                        <th class="p-3 pb-3">Account Label Details</th>
                        <th class="p-3 pb-3 text-right">Opening/Current Balance</th>
                        <th class="p-3 pr-0 pb-3 text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    {{-- লোডিং স্টেট --}}
                    <tr x-show="loading"><td colspan="4" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading Chart of Accounts...</td></tr>
                    
                    {{-- কোনো ডাটা না থাকলে --}}
                    <tr x-show="!loading && accounts.filter(a => a.type === currentTab).length === 0">
                        <td colspan="4" class="p-4 text-center text-slate-400">No account ledgers created under this classification tab.</td>
                    </tr>

                    {{-- ডাইনামিকালি ট্যাব ফিল্টার্ড লুপ --}}
                    <template x-for="account in accounts.filter(a => a.type === currentTab)" :key="account.id">
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="p-3 pl-0 font-bold font-mono text-indigo-600" x-text="account.code"></td>
                            <td class="p-3">
                                <p class="font-bold text-slate-800" x-text="account.name"></p>
                                <p class="text-[10px] text-slate-400 font-medium">
                                    Parent: <span x-text="account.parent_name ? account.parent_name : 'Root Standard'"></span>
                                </p>
                            </td>
                            <td class="p-3 text-right font-black font-mono text-slate-900">
                                <span x-text="number_format(account.opening_balance)"></span> ৳
                            </td>
                            <td class="p-3 pr-0 text-center text-slate-400 hover:text-indigo-600 cursor-pointer font-semibold" @click="editAccount(account)">
                                🛠️ Edit
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 🛠️ অ্যাকাউন্ট ক্রিয়েশন ফ্লাই মডাল ফর্ম --}}
    <div x-show="openModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
        x-transition
        style="display: none;">
         
        <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full shadow-xl overflow-hidden" @click.away="openModal = false">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide" x-text="isEdit ? 'Modify Ledger Settings' : 'Create New Ledger Head'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form @submit.prevent="saveAccount()" class="p-6 space-y-4">
                @csrf
                
                {{-- অ্যাকাউন্ট নেম --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Account Head Name *</label>
                    <input type="text" x-model="accountData.name" required placeholder="e.g. Service Sales, Office Rent, Petty Cash" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800">
                </div>

                {{-- অ্যাকাউন্ট ইউনিক কোড --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Account Code (Unique) *</label>
                    <input type="text" x-model="accountData.code" required placeholder="e.g. 4001, 5022, 1011" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-bold font-mono text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- টাইপ চয়েস --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Account Type *</label>
                        <select x-model="accountData.type" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium capitalize">
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>

                    {{-- ওপেনিং ব্যালেন্স --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Opening Balance (৳)</label>
                        <input type="number" step="0.01" x-model="accountData.opening_balance" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-bold font-mono text-slate-800">
                    </div>
                </div>

                {{-- ডাইনামিক ফ্লাই প্যারেন্ট হেড সিলেক্ট --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Parent Account (Optional Hierarchical Tree)</label>
                    <select x-model="accountData.parent_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                        <option value="">-- Main Root Head --</option>
                        <template x-for="parent in accounts.filter(a => a.type === accountData.type && a.id != accountData.id)" :key="parent.id">
                            <option :value="parent.id" x-text="parent.name + ' (' + parent.code + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition" x-text="isEdit ? 'Update Head' : 'Register Head'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- জাভাস্ক্রিপ্ট কারেন্সি বা নম্বর ফরম্যাট হেল্পার ফাংশন --}}
<script>
    function number_format(number) {
        return parseFloat(number).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>

@endsection