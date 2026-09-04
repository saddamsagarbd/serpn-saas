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
    },

    async handleStatusUpdate(orderId, statusType) {
        const isApprove = statusType === 'completed';
        
        const result = await Swal.fire({
            title: isApprove ? 'Approve Style?' : 'Reject Style?',
            text: `Are you sure you want to mark this style as ${statusType}?`,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#10B981' : '#EF4444',
            confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Reject',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/merchandising/mpr-order/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: statusType })
                });

                if (response.ok) {
                    Swal.fire({
                        title: 'Updated!',
                        text: `MPR status updated to ${statusType}.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.fetchOrders(); // Refresh table data
                } else {
                    throw new Error('Network response was not ok');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to update MPR status.', 'error');
            }
        }
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchOrders()" placeholder="Search style code or name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse  overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Buyer</th>
                            <th class="p-4">Image</th>
                            <th class="p-4">Style No</th>
                            <th class="p-4">PO No</th>
                            <th class="p-4">PO RCV DATE</th>
                            <th class="p-4">REQ DLV DATE</th>
                            <th class="p-4">ORDER QNTY</th>
                            <th class="p-4">Currency</th>
                            <th class="p-4 text-right">FOB/PC</th>
                            <th class="p-4 text-right">Amount</th>
                            <th class="p-4 text-center">JOB MODE</th>
                            <th class="p-4 text-center">SHIPMENT STATUS</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="11" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching MPR order...</td></tr>
                        </template>

                        <template x-if="!loading && orders.length === 0">
                            <tr><td colspan="11" class="p-4 text-center text-gray-400">No matching MPR order found.</td></tr>
                        </template>

                        <template x-if="!loading && orders.length > 0">
                            <template x-for="(order, index) in orders" :key="order.id || index">
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-medium text-gray-900" x-html="order.buyer_name"></td>
                                    <td class="p-4">
                                        <img :src="order.image || order.product_image || '/images/default-placeholder.png'" 
                                             :alt="order.product_name"
                                             class="h-16 w-16 object-cover rounded-xl border border-slate-300 shadow-sm">
                                    </td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.style_no"></td>
                                    <td class="p-4 font-mono">
                                        <a :href="'/merchandising/mpr-order-details/' + order.id" 
                                        class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline"
                                        x-text="order.po_no">
                                        </a>
                                    </td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.po_date"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.delivery_date"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.order_qty"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="order.currency"></td>
                                    <td class="p-4 font-medium text-gray-900 text-right" x-text="order.fob"></td>
                                    <td class="p-4 font-medium text-gray-900 text-right" x-text="order.total_amount"></td>
                                    <td class="p-4 font-medium text-gray-900 text-center" x-text="order.job_mode"></td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                            :class="{
                                                'bg-gray-100 text-gray-800': order.status === 'draft',
                                                'bg-blue-100 text-blue-800': order.status === 'running',
                                                'bg-green-100 text-green-800': order.status === 'completed',
                                                'bg-red-100 text-red-800': order.status === 'cancelled',
                                                'bg-red-100 text-red-800': order.status === 'rejected'
                                            }" 
                                            x-text="order.status || 'N/A'">
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3 text-center overflow-visible">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Direct Links -->
                                            <a :href="'/merchandising/mpr-order/' + order.id + '/export-pdf'" 
                                               class="bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                                Export PDF
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

                                                <!-- Dropup menu (Opens upward) -->
                                                <div x-show="open" 
                                                     x-transition
                                                     class="absolute right-0 bottom-full mb-1 w-32 bg-white border border-gray-100 rounded-lg shadow-xl z-50 py-1 text-left text-xs"
                                                     style="display: none;">
                                                    
                                                    <a :href="'/merchandising/mpr-order-edit/' + order.id"
                                                       class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        Edit
                                                    </a>
                                                    <button @click="open = false; handleStatusUpdate(order.id, 'completed')" 
                                                            class="w-full text-left px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 font-medium">
                                                        Approve
                                                    </button>
                                                    <button @click="open = false; handleStatusUpdate(order.id, 'rejected')" 
                                                            class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-medium">
                                                        Reject
                                                    </button>
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