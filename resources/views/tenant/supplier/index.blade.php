@extends('layouts.tenant')
@section('title','Supplier Master')
@section('content')
<div class="space-y-6" x-data="{
        currentTab: 'suppliers', 
        suppliers: [],
        search: '',
        page: 1,
        perPage: 10,
        lastPage: 1,
        total: 0,
        loading: false,
        async fetchSuppliers() {
            this.loading = true;
            try {
                let url = `{{ route('tenant.purchase.suppliers') }}?page=${this.page}&per_page=${this.perPage}&search=${encodeURIComponent(this.search)}`;
                let response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                let data = await response.json();
                
                // সার্ভার রেসপন্স ম্যাপ করা
                this.suppliers = data.data;
                this.lastPage = data.last_page;
                this.total = data.total;
            } catch (error) {
                console.error('Error fetching suppliers:', error);
            } finally {
                this.loading = false;
            }
        },
        nextPage() { if (this.page < this.lastPage) { this.page++; this.fetchSuppliers(); } },
        prevPage() { if (this.page > 1) { this.page--; this.fetchSuppliers(); } }
    }"
    x-init="fetchSuppliers()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'suppliers'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Supplier List</h2>
                <a href="{{ route('tenant.purchase.suppliers.form') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Supplier
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" placeholder="Search suppliers..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Code</th>
                            <th class="p-4">Organization</th>
                            <th class="p-4">Contact Person</th>
                            <th class="p-4">Phone</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Address</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-for="supplier in suppliers" :key="supplier.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 font-semibold text-slate-800" x-text="supplier.supplier_code"></td>
                                <td class="p-4 font-semibold text-slate-800" x-text="supplier.name ?? 'Annonymous'"></td>
                                <td class="p-4 font-semibold text-slate-800" x-text="supplier.contact_person ?? 'Annonymous'"></td>
                                <td class="p-4 text-slate-500" x-text="supplier.phone ?? 'N/A'"></td>
                                <td class="p-4 text-slate-500" x-text="supplier.email ?? 'N/A'"></td>
                                <td class="p-4 font-mono" x-text="supplier.address ?? 'N/A'"></td>
                                <td class="p-4 text-center">
                                    <a :href="`{{ route('tenant.purchase.suppliers') }}/${supplier.id}/edit`" class="text-indigo-600 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg font-bold transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        </template>
                        
                        <tr x-show="suppliers.length === 0 && !loading" x-cloak>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-medium">No records found matching criteria.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 text-xs font-semibold text-slate-500">
                <div>
                    Showing <span class="text-slate-800" x-text="suppliers.length"></span> of <span class="text-slate-800" x-text="total"></span> records
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