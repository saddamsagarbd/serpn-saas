@extends('layouts.tenant')
@section('title', 'Supplier Invoices')

@section('content')
<div class="space-y-6" x-data="{ 
    invoices: [],
    loading: false,
    searchQuery: '',

    fetchInvoices() {
        this.loading = true;
        let url = '{{ route('tenant.purchase.invoice.index') }}';
        if (this.searchQuery) {
            url += '?search=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.invoices = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    formatCurrency(amount) {
        return parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ৳';
    }
}" x-init="fetchInvoices()">

    @if(session('success'))
        <div class="p-4 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm overflow-visible">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Supplier Invoices (Bills)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage vendor bills, payment statuses, and 3-way matching records.</p>
                </div>
                <a href="{{ route('tenant.purchase.invoice.create') }}" class="bg-rose-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-rose-700 shadow-sm transition">
                    + Create New Invoice
                </a>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <!-- Search Control Bar -->
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Accounts Payable Invoices</span>
                    <input type="text" 
                        x-model="searchQuery" 
                        @input.debounce.500ms="fetchInvoices()" 
                        placeholder="Search invoice no, supplier or GRN..." 
                        class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-rose-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Invoice No</th>
                            <th class="p-4">Supplier</th>
                            <th class="p-4">GRN Ref</th>
                            <th class="p-4 text-right">Net Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <!-- Loading State -->
                        <template x-if="loading">
                            <tr>
                                <td colspan="6" class="p-4 text-center text-rose-600 font-semibold animate-pulse">
                                    Fetching supplier invoice records...
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!loading && invoices.length === 0">
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                    No purchase invoices or vendor bills recorded yet.
                                </td>
                            </tr>
                        </template>

                        <!-- Data Rows -->
                        <template x-if="!loading && invoices.length > 0">
                            <template x-for="(invoice, index) in invoices" :key="invoice.id || index">
                                <tr class="hover:bg-gray-50/80 transition relative">
                                    
                                    <!-- Invoice Number & Date -->
                                    <td class="p-4 pl-6">
                                        <span class="font-bold text-rose-600 block" x-text="invoice.invoice_no"></span>
                                        <span class="text-[10px] text-slate-400" x-text="formatDate(invoice.invoice_date)"></span>
                                    </td>

                                    <!-- Supplier Name -->
                                    <td class="p-4 font-medium text-gray-900" x-text="invoice.supplier ? invoice.supplier.name : 'N/A'"></td>

                                    <!-- GRN Ref -->
                                    <td class="p-4 font-medium text-gray-900" x-text="invoice.grn ? invoice.grn.grn_no : 'N/A'"></td>

                                    <!-- Net Amount -->
                                    <td class="p-4 text-right font-bold font-mono text-rose-600" x-text="formatCurrency(invoice.net_amount || invoice.grand_total)"></td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full capitalize"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': invoice.status === 'unpaid',
                                                'bg-blue-100 text-blue-800': invoice.status === 'partially_paid',
                                                'bg-emerald-100 text-emerald-800': invoice.status === 'paid',
                                                'bg-rose-100 text-rose-800': invoice.status === 'cancelled'
                                            }" 
                                            x-text="invoice.status ? invoice.status.replace('_', ' ') : 'N/A'">
                                        </span>
                                    </td>

                                    <!-- Action Column -->
                                    <td class="px-4 py-3 text-center overflow-visible">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Print / View Link -->
                                            <a :href="`/purchase/suppliers/invoice/${invoice.id}/print`" 
                                            target="_blank"
                                            class="bg-rose-50 border border-rose-100 text-rose-600 px-2.5 py-1 rounded-lg hover:bg-rose-600 hover:text-white font-semibold transition text-xs">
                                                Print
                                            </a>

                                            <!-- Alpine Action Dropdown -->
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button @click="open = !open" 
                                                        type="button" 
                                                        class="bg-gray-50 border border-gray-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold transition text-xs inline-flex items-center gap-1">
                                                    More
                                                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Dropup menu -->
                                                <div x-show="open" 
                                                        x-transition
                                                        class="absolute right-0 bottom-full mb-1 w-36 bg-white border border-gray-100 rounded-lg shadow-xl z-50 py-1 text-left text-xs"
                                                        style="display: none;">
                                                    
                                                    <a :href="`/purchase/suppliers/invoice/${invoice.id}`" 
                                                        class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        View Details
                                                    </a>
                                                    
                                                    <a :href="`/purchase/suppliers/invoice/${invoice.id}/edit`" 
                                                        class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        Edit Invoice
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection