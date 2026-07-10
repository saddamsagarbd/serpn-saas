@extends('layouts.tenant')
@section('title','Category')
@section('content')
<div class="space-y-6" x-data="{ 
    openModal: false, 
    isEdit: false,
    formAction: '{{ route('tenant.inventory.categories.store') }}',
    categoryData: { id: '', name: '', parent_id: '', description: '' },
    categories: [],
    loading: false,
    searchQuery: '',
    
    initCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('tenant.inventory.categories.store') }}';
        this.categoryData = { id: '', name: '', parent_id: '', description: '' };
        this.openModal = true;
    },
    editCategory(data) {
        console.log(data);
        this.isEdit = true;

        let baseUrl = '{{ route("tenant.inventory.categories.update", ["id" => ":id"]) }}';
        this.formAction = baseUrl.replace(':id', data.id);
        
        this.categoryData = { 
            id: data.id, 
            name: data.name, 
            parent_id: data.parent_id ?? '', 
            description: data.description ?? '' 
        };
        this.openModal = true;
    },
    fetchCategories() {
        this.loading = true;
        let url = '{{ route('tenant.inventory.categories.index') }}';
        
        if (this.searchQuery) {
            url += '?search[value]=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            this.categories = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error fetching categories:', err);
            this.loading = false;
        });
    },
    saveCategory() {
        const token = document.querySelector('input[name=\'_token\']')?.value;

        let formData = {
            name: this.categoryData.name,
            parent_id: this.categoryData.parent_id || null,
            description: this.categoryData.description || ''
        };

        fetch(this.formAction, {
            method: this.isEdit ? 'PUT' : 'POST',   // ← real method, no _method needed
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) throw new Error('Validation or Server Error');
            return response.json();
        })
        .then(data => {
            this.openModal = false;
            this.fetchCategories();
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || 'Saved successfully!');
            }
        })
        .catch(err => {
            console.error('Error saving category:', err);
            alert('Failed to save category. Please check your inputs.');
        });
    }
}" x-init="fetchCategories()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Category Master</h2>
                <button @click="initCreate()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Category
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" id="customSearch" placeholder="Search categories..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Code</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Parent Category</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        {{-- লোডিং স্টেট --}}
                        <template x-if="loading">
                            <tr>
                                <td colspan="4" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">
                                    Loading Category Vault...
                                </td>
                            </tr>
                        </template>

                        {{-- ডাটা না থাকলে নোটিফিকেশন --}}
                        <template x-if="!loading && categories.length === 0">
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-400">
                                    No categories found.
                                </td>
                            </tr>
                        </template>

                        {{-- ৪. Alpine.js লুপ দিয়ে ডাইনামিক ডাটা রেন্ডারিং --}}
                        <template x-if="!loading && categories && categories.length > 0">
                            <template x-for="(cat, index) in categories" :key="cat.id || index">
                                <template x-if="cat && cat.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="cat.code"></td>
                                        <td class="p-4 font-semibold text-gray-900" x-text="cat.name"></td>
                                        <td class="p-4 font-mono text-xs text-gray-500" x-text="cat.parent ? cat.parent : 'N/A'"></td>
                                        
                                        {{-- ৫. অ্যাকশন বাটন এবং ফর্ম হ্যান্ডলিং --}}
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editCategory(cat)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">
                                                Edit
                                            </button>
                                            
                                            {{-- ভ্যানিলা জাভাস্ক্রিপ্ট ডিলিট ট্রিগার --}}
                                            <button @click="if(confirm('Are you sure?')) document.getElementById('del-form-' + cat.id).submit()" 
                                                    class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">
                                                Delete
                                            </button>

                                            {{-- ব্যাকএন্ডে ডিলিট রিকোয়েস্ট পাঠানোর জন্য হিডেন ফর্ম --}}
                                            <form :id="'del-form-' + cat.id" :action="'tenant/inventory/categories/' + cat.id" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
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
    
    @include('slices.category-modal')
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        const el = document.querySelector('[x-data]');
        if (el && el.__x) {
            el.__x.$data.fetchCategories();
        }
    });
</script>
@endpush