@extends('layouts.tenant')
@section('title', 'Style Master List')
@section('content')

<div class="space-y-6" x-data="{ 
    styles: [],
    loading: false,
    searchQuery: '',

    fetchStyles() {
        this.loading = true;
        let url = '{{ route('tenant.merch.styles') }}';
        if (this.searchQuery) {
            url += '?search=' + encodeURIComponent(this.searchQuery);
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

    async handleStatusUpdate(styleId, statusType) {
        const isApprove = statusType === 'completed';
        
        const result = await Swal.fire({
            title: isApprove ? 'Approve Style?' : 'Reject Style?',
            text: `Are you sure you want to mark this style as ${statusType}?`,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#10B981' : '#EF4444',
            confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Reject',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/merchandising/styles/${styleId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: statusType })
                });

                if (response.ok) {
                    Swal.fire({
                        title: 'Updated!',
                        text: `Style status updated to ${statusType}.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.fetchStyles(); // Refresh table data
                } else {
                    throw new Error('Network response was not ok');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to update style status.', 'error');
            }
        }
    }
}" x-init="fetchStyles()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm overflow-visible">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Style Master Data Feed</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Central hub for buying house style specification sheets.</p>
                </div>
                <a href="{{ route('tenant.merch.styles.create') }}" class="bg-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                    + Add New Style
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchStyles()" placeholder="Search style code or name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Picture</th>
                            <th class="p-4">Style Code</th>
                            <th class="p-4">Style Name</th>
                            <th class="p-4">Buyer</th>
                            <th class="p-4">Season</th>
                            <th class="p-4 text-right">FOB</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="8" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching global style profiles...</td></tr>
                        </template>

                        <template x-if="!loading && styles.length === 0">
                            <tr><td colspan="8" class="p-4 text-center text-gray-400">No matching style master profiles found.</td></tr>
                        </template>

                        <template x-if="!loading && styles.length > 0">
                            <template x-for="(style, index) in styles" :key="style.id || index">
                                <tr class="hover:bg-gray-50/80 transition relative">
                                    <!-- Image Column -->
                                    <td class="p-4">
                                        <img :src="style.image || style.product_image || '/images/default-placeholder.png'" 
                                             :alt="style.product_name"
                                             class="h-16 w-16 object-cover rounded-xl border border-slate-300 shadow-sm">
                                    </td>

                                    <!-- Style Code Link -->
                                    <td class="p-4 font-mono">
                                        <a :href="`/merchandising/styles/${style.id}/details`" 
                                           class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline"
                                           x-text="style.style_code || style.style_number || 'N/A'">
                                        </a>
                                    </td>

                                    <!-- Product Name -->
                                    <td class="p-4 font-medium text-gray-900" x-text="style.product_name || 'N/A'"></td>

                                    <!-- Buyer & Season (HTML decode enabled for buyer name) -->
                                    <td class="p-4 text-gray-500" x-html="style.buyer ? style.buyer.name : (style.buyer_name || 'N/A')"></td>
                                    <td class="p-4 text-gray-500" x-text="style.season ? style.season.name : (style.season_name || 'N/A')"></td>

                                    <!-- Costing -->
                                    <td class="p-4 text-right font-bold font-mono text-slate-800" 
                                        x-text="style.costing?.offered_fob ? '$' + parseFloat(style.costing.offered_fob).toFixed(4) : '$0.0000'">
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                            :class="{
                                                'bg-gray-100 text-gray-800': style.status === 'draft',
                                                'bg-blue-100 text-blue-800': style.status === 'running',
                                                'bg-green-100 text-green-800': style.status === 'completed',
                                                'bg-red-100 text-red-800': style.status === 'cancelled',
                                                'bg-red-100 text-red-800': style.status === 'rejected'
                                            }" 
                                            x-text="style.status || 'N/A'">
                                        </span>
                                    </td>

                                    <!-- Action Column -->
                                    <td class="px-4 py-3 text-center overflow-visible">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Direct Links -->
                                            <a :href="`/merchandising/styles/${style.id}/details`" 
                                               class="bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                                Costing
                                            </a>
                                            <a :href="`/merchandising/styles/${style.id}/bom`" 
                                               class="bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                                BOM
                                            </a>

                                            <!-- Alpine Action Dropdown -->
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button @click="open = !open" 
                                                        type="button" 
                                                        class="bg-gray-50 border border-gray-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold transition text-xs inline-flex items-center gap-1">
                                                    More
                                                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Dropup menu (Opens upward) -->
                                                <div x-show="open" 
                                                     x-transition
                                                     class="absolute right-0 bottom-full mb-1 w-32 bg-white border border-gray-100 rounded-lg shadow-xl z-50 py-1 text-left text-xs"
                                                     style="display: none;">
                                                    
                                                    <a :href="`/merchandising/styles/${style.id}/edit`" 
                                                       class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        Edit
                                                    </a>
                                                    <button @click="open = false; handleStatusUpdate(style.id, 'completed')" 
                                                            class="w-full text-left px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 font-medium">
                                                        Approve
                                                    </button>
                                                    <button @click="open = false; handleStatusUpdate(style.id, 'rejected')" 
                                                            class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-medium">
                                                        Reject
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