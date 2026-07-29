@extends('layouts.tenant')
@section('title', 'Size Chart Master')
@section('content')

<div class="space-y-6" x-data="{ 
    openModal: false, 
    isEdit: false,
    formAction: '{{ route('tenant.inventory.sizes.store') }}',
    
    // ইউনিট ডেটা মডেল
    sizeData: { 
        id: '', 
        name: '', 
        short_name: '',
    },
    
    sizes: [],
    loading: false,
    searchQuery: '',

    initCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('tenant.inventory.sizes.store') }}';
        this.sizeData = { id: '', name: '', short_name: '', };
        this.openModal = true;
    },

    editSize(data) {
        this.isEdit = true;
        let baseUrl = '{{ route("tenant.inventory.sizes.update", ":id") }}';
        this.formAction = baseUrl.replace(':id', data.id);
        
        this.sizeData = { 
            id: data.id, 
            name: data.name, 
            short_name: data.short_name,
        };
        this.openModal = true;
    },

    fetchSizes() {
        this.loading = true;
        let url = '{{ route('tenant.inventory.sizes') }}';
        if (this.searchQuery) {
            url += '?search[value]=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.sizes = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    saveSize() {
        const token = document.querySelector('input[name=\'_token\']')?.value;
        let formData = { ...this.sizeData };

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
            if (!response.ok) throw new Error('Validation Error');
            return response.json();
        })
        .then(data => {
            this.openModal = false;
            this.fetchSizes();
            if (typeof toastr !== 'undefined') toastr.success(data.message || 'Success');
        })
        .catch(err => alert('Failed to save size. Please check inputs.'));
    }
}" x-init="fetchSizes()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Size Chart Master</h2>
                <button @click="initCreate()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Size Chart
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">ERP Standard Measurement Feed</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchSizes()" placeholder="Search sizes..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Name</th>
                            <th class="p-4">Short Name</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="5" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading Size chart...</td></tr>
                        </template>

                        <template x-if="!loading && sizes.length === 0">
                            <tr><td colspan="5" class="p-4 text-center text-gray-400">No size chart found.</td></tr>
                        </template>

                        <template x-if="!loading && sizes.length > 0">
                            <template x-for="(size, index) in sizes" :key="size.id || index">
                                <template x-if="size && size.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-semibold text-gray-900" x-text="size.name"></td>
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="size.short_name"></td>
                                        
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editSize(size)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
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

    @include('slices.size-chart-modal')
</div>
@endsection