@extends('layouts.tenant')
@section('title','Purchase Order (PO)')
@section('content')
<div class="space-y-6" x-data="{
        orders: [],
        search: '',
        page: 1,
        perPage: 10,
        lastPage: 1,
        total: 0,
        loading: false,
        async fetchPurchaseOrders() {
            this.loading = true;
            try {
                let url = `{{ route('tenant.purchase.po.index') }}?page=${this.page}&per_page=${this.perPage}&search=${encodeURIComponent(this.search)}`;
                let response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                let data = await response.json();
                
                // সার্ভার রেসপন্স ম্যাপ করা
                this.orders = data.data;
                console.log(this.orders);
                this.lastPage = data.last_page;
                this.total = data.total;
            } catch (error) {
                console.error('Error fetching orders:', error);
            } finally {
                this.loading = false;
            }
        },
        async handleStatusUpdate(poId, statusType) {
            const isApprove = statusType === 'approved';
            
            const result = await Swal.fire({
                title: (statusType?.toUpperCase() || 'Update') + ' PO?',
                text: `Are you sure you want to mark this PO as ${statusType}?`,
                icon: isApprove ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isApprove ? '#10B981' : '#EF4444',
                confirmButtonText: 'Proceed',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/purchase/po/${poId}/status`, {
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
                            text: `PO status updated to ${statusType}.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        this.fetchPurchaseOrders(); // Refresh table data
                    } else {
                        throw new Error('Network response was not ok');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Failed to update PO status.', 'error');
                }
            }
        },
        nextPage() { if (this.page < this.lastPage) { this.page++; this.fetchPurchaseOrders(); } },
        prevPage() { if (this.page > 1) { this.page--; this.fetchPurchaseOrders(); } }
    }"
    x-init="fetchPurchaseOrders()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Purchase Orders List</h2>
                <a href="{{ route('tenant.purchase.po.create') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Create PO
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase"></span>
                    <input type="text" placeholder="Search orders..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">PO Number</th>
                            <th class="p-4">Supplier Info</th>
                            <th class="p-4">Order Details</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-for="order in orders" :key="order.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 font-semibold text-slate-800" x-text="order.po_no"></td>
                                <td class="p-4 font-semibold text-slate-800" x-text="order.supplier_details"></td>
                                <td class="p-4 font-semibold text-slate-800" x-text="order.order_details"></td>
                                <!-- Status Badge -->
                                <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                        :class="{
                                            'bg-gray-100 text-gray-800': order.status === 'draft',
                                            'bg-blue-100 text-blue-800': order.status === 'pending',
                                            'bg-green-100 text-green-800': order.status === 'approved',
                                            'bg-red-100 text-red-800': order.status === 'rejected'
                                        }" 
                                        x-text="order.status || 'N/A'">
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3 text-center overflow-visible">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a :href="`{{ route('tenant.purchase.po.index') }}/${order.id}/edit`" class="text-indigo-600 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg font-bold transition">
                                            Edit
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
                                                <button @click="open = false; handleStatusUpdate(order.id, 'pending')" 
                                                        class="w-full text-left px-3 py-1.5 text-blue-600 hover:bg-blue-50 font-medium">
                                                    Pending
                                                </button>
                                                <button @click="open = false; handleStatusUpdate(order.id, 'approved')" 
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
                        
                        <tr x-show="orders.length === 0 && !loading" x-cloak>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-medium">No records found matching criteria.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 text-xs font-semibold text-slate-500">
                <div>
                    Showing <span class="text-slate-800" x-text="orders.length"></span> of <span class="text-slate-800" x-text="total"></span> records
                </div>
                
                <div class="flex items-center gap-2">
                    <button @click="prevPage()" 
                            :disabled="page === 1" 
                            :class="page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-800'"
                            class="px-3 py-1.5 border border-slate-200 rounded-lg transition">
                        ◀ Prev
                    </button>
                    
                    <div class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                        Page <span x-text="page"></span> of <span x-text="lastPage"></span>
                    </div>

                    <button @click="nextPage()" 
                            :disabled="page === lastPage" 
                            :class="page === lastPage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-800'"
                            class="px-3 py-1.5 border border-slate-200 rounded-lg transition">
                        Next ▶
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection