@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ currentTab: 'tenants', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <!-- TENANTS TAB CONTENT -->
        <div x-show="currentTab === 'tenants'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Tenant / Vendor Accounts</h2>
                <button @click="openModal = true" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add New Tenant
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" placeholder="Search tenants..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Tenant Code</th>
                            <th class="p-4">Subdomain / Live Link</th>
                            <th class="p-4">Isolated DB Scheme</th>
                            <th class="p-4">Plan Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($tenants as $tenant)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-indigo-600">{{ $tenant->id }}</td>
                                <td class="p-4">
                                    @foreach($tenant->domains as $domain)
                                        <a href="http://{{ $domain->domain }}{{ request()->getPort() ? ':'.request()->getPort() : '' }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $domain->domain }} 🔗
                                        </a>
                                    @endforeach
                                </td>
                                <td class="p-4 font-mono text-xs text-gray-500">tenant_{{ $tenant->id }}</td>
                                <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">Active Premium</span></td>
                                <td class="p-4 text-center">
                                    <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-gray-200 font-semibold">Manage</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-medium">No tenants active inside the architecture database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('slices.tenant-modal')
</div>
@endsection