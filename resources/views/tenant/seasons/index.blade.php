@extends('layouts.tenant')
@section('title', 'Season Management')
@section('content')

<div class="space-y-6" x-data="seasonApp()" x-init="fetchSeasons()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Season Master Feed</h2>
                <p class="text-xs text-slate-400 mt-0.5">Define production timelines and calendar cycles.</p>
            </div>
            <button @click="openModal = true" class="bg-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                + Add Season
            </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden max-w-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                        <th class="p-4 w-2/12">#</th>
                        <th class="p-4">Season Code / Name</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                    <template x-if="loading">
                        <tr><td colspan="2" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading seasons...</td></tr>
                    </template>
                    <template x-if="!loading && seasons.length === 0">
                        <tr><td colspan="2" class="p-4 text-center text-gray-400">No seasonal timelines defined.</td></tr>
                    </template>
                    <template x-if="!loading && seasons.length > 0">
                        <template x-for="(season, index) in seasons" :key="season.id">
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="p-4 font-mono font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="p-4 font-bold text-slate-900 tracking-wide font-mono" x-text="season.name"></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="openModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-xs overflow-hidden" @click.away="openModal = false">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800">Add Season</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form @submit.prevent="submitForm" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Season Name *</label>
                    <input type="text" x-model="formData.name" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., Spring 2026 / EF26" required>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Save Cycle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function seasonApp() {
    return {
        seasons: [],
        loading: false,
        openModal: false,
        formData: { name: '' },

        fetchSeasons() {
            this.loading = true;
            fetch("{{ route('tenant.inventory.seasons') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => { this.seasons = data.data || []; this.loading = false; });
        },
        submitForm() {
            fetch("{{ route('tenant.inventory.seasons.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(this.formData)
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