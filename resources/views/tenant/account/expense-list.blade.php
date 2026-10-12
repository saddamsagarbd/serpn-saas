@extends('layouts.tenant')
@section('title', 'Expense Vouchers List')
@section('content')

<div class="space-y-5 max-w-7xl mx-auto" 
     x-data="{
        expenses: [],
        loading: false,
        searchQuery: '',
        fetchExpenses() {
            this.loading = true;
            let url = '{{ route('tenant.accounts.expense.list') }}';
            if (this.searchQuery) {
                url += '?search=' + encodeURIComponent(this.searchQuery);
            }

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(res => {
                console.log(res.data);
                this.expenses = res.data || [];
                this.loading = false;
            })
            .catch(err => {
                console.error('Error:', err);
                this.loading = false;
            });
        },
     }" x-init="fetchExpenses()">
    
    {{-- টপ হেডার ও অ্যাকশন বাটন --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide">🔴 Expense Vouchers Ledger</h2>
            <p class="text-xs text-slate-400 mt-0.5">Manage and track all recorded expense transactions in real-time.</p>
        </div>
        <div>
            <a href="{{ route('tenant.accounts.expense') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                + Create New Expense
            </a>
        </div>
    </div>

    {{-- এক্সপেন্স লিস্ট টেবিল কার্ড --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
            <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchExpenses()" placeholder="Search expense..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
        </div>
        
        <table class="w-full text-left border-collapse overflow-visible">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                    <th class="p-4">Date</th>
                    <th class="p-4">Voucher No</th>
                    <th class="p-4">Vendor / Party</th>
                    <th class="p-4">Narration</th>
                    <th class="p-4">Expense Head</th>
                    <th class="p-4">Paid From (Account)</th>
                    <th class="p-4">Gateway / Ref</th>
                    <th class="p-4 text-right">Amount (৳)</th>
                </tr>
            </thead>
            <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                <template x-if="loading">
                    <tr><td colspan="8" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching expenses...</td></tr>
                </template>

                <template x-if="!loading && expenses.length === 0">
                    <tr><td colspan="8" class="p-4 text-center text-gray-400">No matching expense found.</td></tr>
                </template>

                <template x-if="!loading && expenses.length > 0">
                    <template x-for="(expense, index) in expenses" :key="expense.id || index">
                        <tr class="hover:bg-gray-50/80 transition relative">
                            <!-- Product Name -->
                            <td class="p-4 font-medium text-gray-900" x-text="expense.date || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.voucher_no || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.party_name || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.narration || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.expense_head || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.payment_head || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.payment_gateway || 'N/A'"></td>
                            <td class="p-4 font-medium text-gray-900" x-text="expense.amount || 'N/A'"></td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</div>

@endsection