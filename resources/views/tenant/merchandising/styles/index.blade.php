@extends('layouts.tenant')
@section('title', 'Style Master List')
@section('content')

<div class="space-y-6" x-data="{ 
    styles: [],
    loading: false,
    searchQuery: '',

    fetchStyles() {
        this.loading = true;
        let url = '{{ route('tenant.merch.styles') }}'; // কনফিগারেশন রুট অনুযায়ী আপডেট
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
    }
}" x-init="fetchStyles()">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchStyles()" placeholder="Search style code or name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Style Code</th>
                            <th class="p-4">Style Name</th>
                            <th class="p-4">Buyer</th>
                            <th class="p-4">Season</th>
                            <th class="p-4 text-right">Target Price</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="6" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Fetching global style profiles...</td></tr>
                        </template>

                        <template x-if="!loading && styles.length === 0">
                            <tr><td colspan="6" class="p-4 text-center text-gray-400">No matching style master profiles found.</td></tr>
                        </template>

                        <template x-if="!loading && styles.length > 0">
                            <template x-for="(style, index) in styles" :key="style.id || index">
                                <tr class="hover:bg-gray-50/80 transition">
                                    <!-- 1. Clickable Style Code Link to go directly to details preview -->
                                    <td class="p-4 font-mono">
                                        <a :href="'/merchandising/styles/' + style.id + '/details'" 
                                        class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline"
                                        x-text="style.style_number">
                                        </a>
                                    </td>
                                    
                                    <!-- Fixed field mapping: style.product_name instead of style.style_name -->
                                    <td class="p-4 font-medium text-gray-900" x-text="style.product_name"></td>
                                    
                                    <td class="p-4 text-gray-500" x-text="style.buyer ? style.buyer.name : 'N/A'"></td>
                                    <td class="p-4 text-gray-500" x-text="style.season ? style.season.name : 'N/A'"></td>
                                    <td class="p-4 text-right font-bold font-mono text-slate-800" x-text="style.costing ? '$' + parseFloat(style.costing.target_fob).toFixed(4) : '$0.0000'"></td>
                                    
                                    <td class="p-4 text-center space-x-2 whitespace-nowrap">
                                        <!-- 2. Preview Details Button -->
                                        <a :href="'/merchandising/styles/' + style.id + '/details'" 
                                        class="inline-block bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                            Preview
                                        </a>

                                        <a :href="'/merchandising/styles/' + style.id + '/bom'" 
                                        class="inline-block bg-indigo-50 border border-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white font-semibold transition text-xs">
                                            Preview
                                        </a>

                                        <!-- Edit Button -->
                                        <a :href="'/merchandising/styles/' + style.id + '/edit'" 
                                        class="inline-block bg-gray-50 border border-slate-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold transition text-xs">
                                            Edit
                                        </a>
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