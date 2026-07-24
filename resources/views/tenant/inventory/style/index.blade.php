@extends('layouts.tenant')
@section('title', 'Style Master')
@section('content')

<div class="space-y-6" x-data="{ 
    openModal: false, 
    isEdit: false,
    formAction: '{{ route('tenant.inventory.styles.store') }}',
    
    // ইউনিট ডেটা মডেল
    styleData: { 
        id: '', 
        name: '', 
        code: '', 
    },
    
    styles: [],
    loading: false,
    searchQuery: '',

    initCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('tenant.inventory.styles.store') }}';
        this.styleData = { id: '', name: '', code: '',};
        this.openModal = true;
    },

    editStyle(data) {
        this.isEdit = true;
        let baseUrl = '{{ route("tenant.inventory.styles.update", ":id") }}';
        this.formAction = baseUrl.replace(':id', data.id);
        
        this.styleData = { 
            id: data.id, 
            name: data.name,
        };
        this.openModal = true;
    },

    fetchStyles() {
        this.loading = true;
        let url = '{{ route('tenant.inventory.styles') }}';
        if (this.searchQuery) {
            url += '?search[value]=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.styles = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    saveStyle() {
        const token = document.querySelector('input[name=\'_token\']')?.value;
        let formData = { ...this.styleData };

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
            this.fetchStyles();
            if (typeof toastr !== 'undefined') toastr.success(data.message || 'Success');
        })
        .catch(err => alert('Failed to save unit. Please check inputs.'));
    }
}" x-init="fetchStyles()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Style Master</h2>
                <a href="{{ route('tenant.inventory.styles.create') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Style
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">ERP Standard Measurement Feed</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchStyles()" placeholder="Search styles..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Name</th>
                            <th class="p-4">Style Code</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="5" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading Styles...</td></tr>
                        </template>

                        <template x-if="!loading && styles.length === 0">
                            <tr><td colspan="5" class="p-4 text-center text-gray-400">No styles found.</td></tr>
                        </template>

                        <template x-if="!loading && styles.length > 0">
                            <template x-for="(style, index) in styles" :key="style.id || index">
                                <template x-if="style && style.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="style.name"></td>
                                        <td class="p-4 font-semibold text-gray-900" x-text="style.code"></td>
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editStyle(style)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                            <button @click="if(confirm('Are you sure?')) document.getElementById('del-style-' + style.id).submit()" class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                                            <form :id="'del-style-' + style.id" :action="'/inventory/styles/delete/' + style.id" method="POST" class="hidden">
                                                @csrf
                                            </form>
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

    @include('slices.style-modal')
</div>
@endsection