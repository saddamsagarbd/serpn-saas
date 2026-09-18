@extends('layouts.tenant')
@section('title', 'User')

@section('content')
<div class="space-y-6" x-data="{ 
    users: [],
    loading: false,
    searchQuery: '',

    fetchUsers() {
        this.loading = true;
        let url = '{{ route('tenant.user.index') }}';
        if (this.searchQuery) {
            url += '?search=' + encodeURIComponent(this.searchQuery);
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(res => {
            this.users = res.data || [];
            this.loading = false;
        })
        .catch(err => {
            console.error('Error:', err);
            this.loading = false;
        });
    },
}" x-init="fetchUsers()">

    @if(session('success'))
        <div class="p-4 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm overflow-visible">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">User</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage vendor bills, payment statuses, and 3-way matching records.</p>
                </div>
                <a href="{{ route('tenant.user.create') }}" class="bg-rose-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-rose-700 shadow-sm transition">
                    + Create New Invoice
                </a>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <!-- Search Control Bar -->
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Accounts Payable Invoices</span>
                    <input type="text" 
                        x-model="searchQuery" 
                        @input.debounce.500ms="fetchUsers()" 
                        placeholder="Search invoice no, supplier or GRN..." 
                        class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-rose-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse overflow-visible">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Sl</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Phone</th>
                            <th class="p-4">Role</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        <!-- Loading State -->
                        <template x-if="loading">
                            <tr>
                                <td colspan="6" class="p-4 text-center text-rose-600 font-semibold animate-pulse">
                                    Fetching user records...
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!loading && users.length === 0">
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                    No users recorded yet.
                                </td>
                            </tr>
                        </template>

                        <!-- Data Rows -->
                        <template x-if="!loading && users.length > 0">
                            <template x-for="(user, index) in users" :key="user.id || index">
                                <tr class="hover:bg-gray-50/80 transition relative">

                                    <!-- User -->
                                    <td class="p-4 font-medium text-gray-900" x-text="typeof index !== 'undefined' ? index + 1 : user.id"></td>

                                    <td class="p-4 font-medium text-gray-900" x-text="user.name ?? 'N/A'"></td>

                                    <!-- Email -->
                                    <td class="p-4 font-medium text-gray-900" x-text="user.email ?? 'N/A'"></td>

                                    <!-- Phone -->
                                    <td class="p-4 font-bold font-mono text-gray-600" x-text="user.phone ?? 'N/A'"></td>

                                    <!-- Status Badge -->
                                    <td class="p-4 font-bold font-mono text-gray-600" x-html="user.role ?? 'N/A'"></td>

                                    <!-- Action Column -->
                                    <td class="px-4 py-3 text-center overflow-visible">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Print / View Link -->                                           

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

                                                <!-- Dropup menu -->
                                                <div x-show="open" 
                                                        x-transition
                                                        class="absolute right-0 bottom-full mb-1 w-36 bg-white border border-gray-100 rounded-lg shadow-xl z-50 py-1 text-left text-xs"
                                                        style="display: none;">
                                                    
                                                    <a :href="`/user/${user.id}/permission`" 
                                                        class="block px-3 py-1.5 text-gray-700 hover:bg-gray-50 font-medium">
                                                        Permission
                                                    </a>
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