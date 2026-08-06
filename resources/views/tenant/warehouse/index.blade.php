@extends('layouts.tenant')
@section('title','Warehouse')
@section('content')
<div class="space-y-6" x-data="warehouseApp()" x-init="fetchWarehouses()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Warehouse List</h2>
                <button @click="openCreateModal()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Warehouse
                </button>
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
                            <th class="p-4">Name</th>
                            <th class="p-4">Address</th>
                            <th class="p-4">Is Default</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr>
                                <td colspan="5" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">
                                    Loading warehouse master...
                                </td>
                            </tr>
                        </template>

                        <template x-if="!loading && warehouses.length === 0">
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-400">
                                    No warehouse found.
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loading && warehouses && warehouses.length > 0">
                            <template x-for="(warehouse, index) in warehouses" :key="warehouse.id || index">
                                <template x-if="warehouse && warehouse.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="warehouse.code"></td>
                                        <td class="p-4 font-semibold text-gray-900" x-text="warehouse.name"></td>
                                        <td class="p-4 font-mono text-xs text-gray-500" x-text="warehouse.address"></td>
                                        <td class="p-4 font-mono text-xs text-gray-500" x-html="warehouse.is_default"></td>
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editWarehouse(warehouse)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('slices.warehouse-modal')
</div>
<script>
function warehouseApp() {
    return {
        warehouses: [],
        isEdit: false,
        loading: false,
        openModal: false,
        searchQuery: '',
        formAction: "{{ route('tenant.inventory.warehouses.store') }}",
        formData: { name: '',  address: '',  is_default: false },
        // নতুন বায়ার ক্রিয়েট মোডাল ওপেন করার ফ্রেশ ফাংশন
        openCreateModal() {
            this.isEdit = false;
            this.formAction = "{{ route('tenant.inventory.warehouses.store') }}";
            this.formData = { name: '' };
            this.openModal = true;
        },
        editWarehouse(data) {
            this.isEdit = true;
            let baseUrl = '{{ route("tenant.inventory.warehouses.update", ["id" => ":id"]) }}';
            this.formAction = baseUrl.replace(':id', data.id);
            
            this.formData = { 
                id: data.id, 
                name: data.name ?? '',
                address: data.address ?? '',
                is_default: data.is_default ?? false,
            };
            this.openModal = true;
        },
        fetchWarehouses() {
            this.loading = true;
            let url = "{{ route('tenant.inventory.warehouses.index') }}";
            if (this.searchQuery) url += '?search[value]=' + encodeURIComponent(this.searchQuery);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => { this.warehouses = data.data || []; this.loading = false; });
        },
        submitForm() {
            let payload = { ...this.formData };
            if (this.isEdit) {
                payload._method = 'PUT'; 
            }
            fetch(this.formAction, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.openModal = false;
                    this.formData = { name: '',  address: '',  is_default: false };
                    this.fetchWarehouses();
                }
            });
        }
    }
}
</script>
@endsection