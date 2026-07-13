@extends('layouts.tenant')
@section('title', 'Style Master')
@section('content')

<div class="space-y-6" x-data="{ 
    openModal: false, 
    isEdit: false,
    formAction: '{{ route('tenant.inventory.color.store') }}',
    
    // ইউনিট ডেটা মডেল
    colorData: { 
        id: '', 
        name: '', 
        color_code: '', 
    },
    
    colors: [],
    loading: false,
    searchQuery: '',

    initCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('tenant.inventory.color.store') }}';
        this.colorData = { id: '', name: '', color_code: '',};
        this.openModal = true;
    },

    editFabric(data) {
        this.isEdit = true;
        let baseUrl = '{{ route("tenant.inventory.color.update", ":id") }}';
        this.formAction = baseUrl.replace(':id', data.id);
        
        this.colorData = { 
            id: data.id, 
            name: data.name,
            color_code: data.color_code,
        };
        this.openModal = true;
    },

    fetchColors() {
        this.loading = true;
        let url = '{{ route('tenant.inventory.color') }}';
        if (this.searchQuery) {
            url += '?search[value]=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.colors = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    saveColor() {
        const token = document.querySelector('input[name=\'_token\']')?.value;
        let formData = { ...this.colorData };

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
            this.fetchColors();
            if (typeof toastr !== 'undefined') toastr.success(data.message || 'Success');
        })
        .catch(err => alert('Failed to save unit. Please check inputs.'));
    }
}" x-init="fetchColors()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Color Context Master</h2>
                <button @click="initCreate()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Color Context
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">ERP Standard Measurement Feed</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchStyles()" placeholder="Search color..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Color</th>
                            <th class="p-4">Color Code</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="5" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading color...</td></tr>
                        </template>

                        <template x-if="!loading && colors.length === 0">
                            <tr><td colspan="5" class="p-4 text-center text-gray-400">No colors found.</td></tr>
                        </template>

                        <template x-if="!loading && colors.length > 0">
                            <template x-for="(color, index) in colors" :key="color.id || index">
                                <template x-if="color && color.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="color.name"></td>
                                        <td class="p-4 font-semibold text-gray-900" x-text="color.color_code"></td>
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editFabric(color)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
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

    @include('slices.color-modal')
</div>
@endsection