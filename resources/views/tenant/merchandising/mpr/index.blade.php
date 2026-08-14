@extends('layouts.tenant')
@section('title', 'MPR Order List')
@section('content')

<div class="space-y-6" x-data="{ 
    orders: [],
    loading: false,
    searchQuery: '',

    fetchOrders() {
        this.loading = true;
        let url = '{{ route('tenant.merch.mpr.index') }}'; // কনফিগারেশন রুট অনুযায়ী আপডেট
        if (this.searchQuery) {
            url += '?search=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.orders = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    }
}" x-init="fetchOrders()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">MPR Order List</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Central hub for buying house style specification sheets.</p>
                </div>
                <a href="{{ route('tenant.merch.mpr.order-create') }}" class="bg-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                    + Create MPR
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchOrders()" placeholder="Search style code or name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Buyer PO No</th>
                            <th class="p-4">Buyer Name</th>
                            <th class="p-4">PO Date</th>
                            <th class="p-4">Del. Date</th>
                            <th class="p-4">Job Mode</th>
                            <th class="p-4">Currency</th>
                            <th class="p-4 text-right">Amount</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="9" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching MPR order...</td></tr>
                        </template>

                        <template x-if="!loading && orders.length === 0">
                            <tr><td colspan="9" class="p-4 text-center text-gray-400">No matching MPR order found.</td></tr>
                        </template>

                        <template x-if="!loading && orders.length > 0">
                            <template x-for="(order, index) in orders" :key="order.id || index">
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-mono">
                                        <a :href="'/merchandising/mpr-order-details/' + order.id" 
                                        class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline"
                                        x-text="order.buyer_po">
                                        </a>
                                    </td>
                                    
                                    <td class="p-4 font-medium text-gray-900" x-text="order.buyer_name"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.po_date"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.delivery_date"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.job_mode"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.currency"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.total_amount"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.status"></td>
                                    
                                    <td class="p-4 text-center space-x-2 whitespace-nowrap">
                                        <a :href="'/merchandising/mpr-order/' + order.id + '/export-pdf'" 
                                        class="inline-block bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                            Export PDF
                                        </a>
                                        <!-- Edit Button -->
                                        <a :href="'/merchandising/mpr-order-edit/' + order.id" 
                                        class="inline-block bg-gray-50 border border-slate-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold transition text-xs cursor-pointer">
                                            Edit
                                        </a>
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