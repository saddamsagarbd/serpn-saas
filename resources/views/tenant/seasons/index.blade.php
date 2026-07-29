@extends('layouts.tenant')
@section('title', 'Season Management')
@section('content')

<div class="space-y-6" x-data="seasonApp()" x-init="fetchSeasons()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Season Master</h2>
                <button @click="openCreateModal()" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Season
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchSeasons()" placeholder="Search buyer name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4 w-2/12">#</th>
                            <th class="p-4">Season Code / Name</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        {{-- লোডিং স্টেট --}}
                        <template x-if="loading">
                            <tr>
                                <td colspan="6" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">
                                    Loading Season Vault...
                                </td>
                            </tr>
                        </template>

                        {{-- ডাটা না থাকলে নোটিফিকেশন --}}
                        <template x-if="!loading && seasons.length === 0">
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-400">
                                    No seasons found.
                                </td>
                            </tr>
                        </template>

                        {{-- ৪. Alpine.js লুপ দিয়ে ডাইনামিক ডাটা রেন্ডারিং --}}
                        <template x-if="!loading && seasons && seasons.length > 0">
                            <template x-for="(season, index) in seasons" :key="season.id || index">
                                <template x-if="season && season.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-indigo-600 font-mono" x-text="index + 1"></td>
                                        <td class="p-4 font-semibold text-gray-900" x-text="season.name"></td>                                        
                                        {{-- ৫. অ্যাকশন বাটন এবং ফর্ম হ্যান্ডলিং --}}
                                        <td class="p-4 text-center space-x-1">
                                            <button @click="editSeason(season)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">
                                                Edit
                                            </button>
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

    <!-- Modal -->
    @include('slices.season-modal')
</div>

<script>
function seasonApp() {
    return {
        seasons: [],
        isEdit: false,
        loading: false,
        openModal: false,
        searchQuery: '',
        formAction: "{{ route('tenant.inventory.seasons.store') }}",
        formData: { name: '' },
        // নতুন বায়ার ক্রিয়েট মোডাল ওপেন করার ফ্রেশ ফাংশন
        openCreateModal() {
            this.isEdit = false;
            this.formAction = "{{ route('tenant.inventory.seasons.store') }}";
            this.formData = { name: '' };
            this.openModal = true;
        },
        editSeason(data) {
            this.isEdit = true;
            let baseUrl = '{{ route("tenant.inventory.seasons.update", ["id" => ":id"]) }}';
            this.formAction = baseUrl.replace(':id', data.id);
            
            this.formData = { 
                id: data.id, 
                name: data.name ?? '',
            };
            this.openModal = true;
        },
        fetchSeasons() {
            this.loading = true;
            let url = "{{ route('tenant.inventory.seasons') }}";
            if (this.searchQuery) url += '?search[value]=' + encodeURIComponent(this.searchQuery);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => { this.seasons = data.data || []; this.loading = false; });
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
                    this.formData = { name: '' };
                    this.fetchSeasons();
                }
            });
        }
    }
}
</script>
@endsection