@extends('layouts.tenant')
@section('title', 'Production BOM List')
@section('content')

<div class="space-y-6" x-data="{ 
    boms: [],
    loading: false,
    searchQuery: '',

    fetchBoms() {
        this.loading = true;
        let url = '{{ route('tenant.merch.bom.index') }}';
        if (this.searchQuery) {
            url += '?search=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.boms = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    async handleDelete(bomId) {
        const result = await Swal.fire({
            title: 'Delete Production BOM?',
            text: 'Are you sure you want to delete this BOM? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/merchandising/bom/${bomId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Production BOM has been deleted.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.fetchBoms();
                } else {
                    throw new Error('Failed to delete');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to delete Production BOM.', 'error');
            }
        }
    }
}" x-init="fetchBoms()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Production BOM List</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Central repository for production bill of materials and execution budgets.</p>
                </div>
                <a href="{{ route('tenant.merch.bom.create') }}" class="bg-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                    + Create Production BOM
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Production BOMs</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchBoms()" placeholder="Search style or buyer..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Style Code</th>
                            <th class="p-4">Style Name</th>
                            <th class="p-4">Buyer</th>
                            <th class="p-4">MPR PO No</th>
                            <th class="p-4 text-right">Order Qty</th>
                            <th class="p-4 text-right">Total Budget</th>
                            <th class="p-4 text-right">Paid Amount</th>
                            <th class="p-4 text-right">Due Amount</th>
                            <th class="p-4 text-center">Created Date</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="10" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching Production BOMs...</td></tr>
                        </template>

                        <template x-if="!loading && boms.length === 0">
                            <tr><td colspan="10" class="p-4 text-center text-gray-400">No matching Production BOM found.</td></tr>
                        </template>

                        <template x-if="!loading && boms.length > 0">
                            <template x-for="(bom, index) in boms" :key="bom.id || index">
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-bold text-indigo-600" x-text="bom.style_code"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="bom.style_name"></td>
                                    <td class="p-4 font-medium text-gray-900" x-text="bom.buyer_name"></td>
                                    <td class="p-4 font-mono font-bold text-gray-800" x-text="bom.mpr_po_no"></td>
                                    <td class="p-4 font-mono text-right" x-text="bom.sales_order_total + ' Pcs'"></td>
                                    <td class="p-4 font-mono text-right font-bold text-slate-900" x-text="'$' + bom.total_budget"></td>
                                    <td class="p-4 font-mono text-right font-bold text-emerald-600" x-text="'$' + bom.total_paid"></td>
                                    <td class="p-4 font-mono text-right font-bold text-rose-600" x-text="'$' + (bom.total_budget - bom.total_paid).toFixed(2)"></td>
                                    <td class="p-4 font-mono text-center text-slate-500" x-text="bom.created_at"></td>
                                    
                                    <td class="px-4 py-3 text-center overflow-visible">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a :href="'/merchandising/bom/' + bom.id + '/export-pdf'" 
                                                class="bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs" target="_blank">
                                                Export PDF
                                            </a>

                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button @click="open = !open" 
                                                        type="button" 
                                                        class="bg-gray-50 border border-gray-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold transition text-xs inline-flex items-center gap-1">
                                                    More
                                                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <div x-show="open" 
                                                    x-transition
                                                    class="absolute right-0 bottom-full mb-1 w-32 bg-white border border-gray-100 rounded-lg shadow-xl z-50 py-1 text-left text-xs"
                                                    style="display: none;">
                                                    
                                                    <a :href="'/merchandising/bom/' + bom.id + '/edit'"
                                                        class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        Edit BOM
                                                    </a>
                                                    <button @click="open = false; handleDelete(bom.id)" 
                                                            class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-medium">
                                                        Delete
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