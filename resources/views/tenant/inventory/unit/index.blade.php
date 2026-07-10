@extends('layouts.tenant')
@section('title', 'Unit Master')
@section('content')

<div class="space-y-6" x-data="{ 
    openModal: false, 
    isEdit: false,
    formAction: '{{ route('tenant.inventory.units.store') }}',
    
    // ইউনিট ডেটা মডেল
    unitData: { 
        id: '', 
        name: '', 
        short_name: '', 
        is_base_unit: '1', {{-- ডিফল্ট হিসেবে Yes বা Base Unit থাকবে --}}
        base_unit_id: '', 
        operator_value: '1.0000', 
        operator: '*' 
    },
    
    units: [],
    loading: false,
    searchQuery: '',

    initCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('tenant.inventory.units.store') }}';
        this.unitData = { id: '', name: '', short_name: '', is_base_unit: '1', base_unit_id: '', operator_value: '1.0000', operator: '*' };
        this.openModal = true;
    },

    editUnit(data) {
        this.isEdit = true;
        let baseUrl = '{{ route("tenant.inventory.units.update", ":id") }}';
        this.formAction = baseUrl.replace(':id', data.id);
        
        this.unitData = { 
            id: data.id, 
            name: data.name, 
            short_name: data.short_name, 
            is_base_unit: String(data.is_base_unit), {{-- স্ট্রিং-এ রূপান্তর করা হয়েছে টগল লজিকের সুবিধার্থে --}}
            base_unit_id: data.base_unit_id ?? '', 
            operator_value: data.operator_value ?? '1.0000', 
            operator: data.operator ?? '*' 
        };
        this.openModal = true;
    },

    fetchUnits() {
        this.loading = true;
        let url = '{{ route('tenant.inventory.units') }}';
        if (this.searchQuery) {
            url += '?search[value]=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.units = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },

    saveUnit() {
        const token = document.querySelector('input[name=\'_token\']')?.value;
        let formData = { ...this.unitData };

        if (this.isEdit) {
            formData._method = 'PUT';
        }

        fetch(this.formAction, {
            method: 'POST',
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
            this.fetchUnits();
            if (typeof toastr !== 'undefined') toastr.success(data.message || 'Success');
        })
        .catch(err => alert('Failed to save unit. Please check inputs.'));
    }
}" x-init="fetchUnits()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Unit Master</h2>
                <button @click="initCreate()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Unit
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">ERP Standard Measurement Feed</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchUnits()" placeholder="Search units..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Name</th>
                            <th class="p-4">Short Name</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Conversion Relation</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="5" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading Units...</td></tr>
                        </template>

                        <template x-if="!loading && units.length === 0">
                            <tr><td colspan="5" class="p-4 text-center text-gray-400">No units found.</td></tr>
                        </template>

                        <template x-if="!loading && units.length > 0">
                            <template x-for="(unit, index) in units" :key="unit.id || index">
                                <template x-if="unit && unit.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-semibold text-gray-900" x-text="unit.name"></td>
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="unit.short_name"></td>
                                        <td class="p-4">
                                            <span :class="unit.is_base_unit ? 'bg-green-50 text-green-700 border-green-200' : 'bg-blue-50 text-blue-700 border-blue-200'" class="px-2.5 py-1 text-xs font-bold rounded-full border">
                                                <span x-text="unit.is_base_unit ? 'Base Unit' : 'Sub Unit'"></span>
                                            </span>
                                        </td>
                                        <td class="p-4 text-xs font-medium text-gray-500 font-mono">
                                            <template x-if="!unit.is_base_unit">
                                                <span>1 <span x-text="unit.short_name"></span> = <span x-text="parseFloat(unit.operator_value)"></span> <span x-text="unit.operator"></span> <span x-text="unit.base_unit ? unit.base_unit : 'Base'"></span></span>
                                            </template>
                                            <template x-if="unit.is_base_unit">
                                                <span class="text-gray-400">N/A (Root Standard)</span>
                                            </template>
                                        </td>
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editUnit(unit)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                            <button @click="if(confirm('Are you sure?')) document.getElementById('del-unit-' + unit.id).submit()" class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                                            <form :id="'del-unit-' + unit.id" :action="'/inventory/units/delete/' + unit.id" method="POST" class="hidden">
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
    
    {{-- মডাল ব্লেড স্লাইস যুক্ত করা হলো --}}
    @include('slices.unit-modal')
</div>
@endsection