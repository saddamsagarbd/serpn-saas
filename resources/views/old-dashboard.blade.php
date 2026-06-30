<x-app-layout>
    <div class="flex h-screen bg-gray-100 font-sans" x-data="{ currentTab: 'dashboard', openModal: false }">
        
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shadow-xl">
            <div class="p-5 text-xl font-bold text-white border-b border-slate-800 flex items-center gap-2">
                🚀 <span class="tracking-wide">SERPN SaaS</span>
            </div>
            
            <nav class="flex-1 p-4 space-y-2 text-sm">
                <button @click="currentTab = 'dashboard'" :class="currentTab === 'dashboard' ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800'" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium">
                    📊 Statistical Dashboard
                </button>
                <button @click="currentTab = 'tenants'" :class="currentTab === 'tenants' ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800'" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium">
                    🏪 Tenant/Organization Management
                </button>
                <button @click="currentTab = 'plans'" :class="currentTab === 'plans' ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800'" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium">
                    💳 Subscription Plans
                </button>
            </nav>
            
            <div class="p-4 border-t border-slate-800 text-xs text-slate-500 text-center">
                v1.0.0 © 2026 SERPN
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-8">

            <div x-show="currentTab === 'dashboard'" x-transition class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800">SaaS Overview Analytics</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Active Tenants</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $tenants->count() }}</p>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600 text-xl">🏢</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Monthly Recurring Revenue</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">৳ ১,৮৫,০০০</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg text-green-600 text-xl">💵</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Premium Subs</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">১২টি</p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-lg text-amber-600 text-xl">⭐</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System Health</p>
                            <p class="text-3xl font-extrabold text-green-600 mt-1">99.9%</p>
                        </div>
                        <div class="p-3 bg-teal-50 rounded-lg text-teal-600 text-xl">⚡</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4">System Activity logs</h3>
                    <div class="text-sm text-gray-500 space-y-3">
                        <p class="flex items-center justify-between p-2.5 bg-gray-50 rounded"><span>🟢 Database <code>tenant_rahim</code> provisioned successfully.</span> <span class="text-xs">2 mins ago</span></p>
                        <p class="flex items-center justify-between p-2.5 bg-gray-50 rounded"><span>🟢 Subdomain <code>rahim.serpn-saas.test</code> routing active.</span> <span class="text-xs">5 mins ago</span></p>
                    </div>
                </div>
            </div>

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

            <div x-show="currentTab === 'plans'" x-transition class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800">Subscription & Price Packaging</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Basic Startup</h3>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded">Active</span>
                        </div>
                        <p class="text-3xl font-extrabold text-indigo-600">৳ ১,৫০০ <span class="text-xs font-normal text-gray-400">/ Monthly</span></p>
                        <ul class="text-xs text-gray-600 space-y-2 border-t pt-3">
                            <li>✔️ Single Warehouse Stock Tracking</li>
                            <li>✔️ Up to 500 Invoices / Month</li>
                            <li>❌ Website E-commerce Integration</li>
                        </ul>
                    </div>

                    <div class="bg-white border-2 border-indigo-600 p-6 rounded-xl shadow-md space-y-4 relative">
                        <div class="absolute top-0 right-6 -translate-y-1/2 bg-indigo-600 text-white text-[10px] font-bold px-3 py-0.5 rounded-full uppercase">Most Popular</div>
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Enterprise POS</h3>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded">Active</span>
                        </div>
                        <p class="text-3xl font-extrabold text-indigo-600">৳ ৩,৫০০ <span class="text-xs font-normal text-gray-400">/ Monthly</span></p>
                        <ul class="text-xs text-gray-600 space-y-2 border-t pt-3">
                            <li>✔️ Unlimited Raw Materials & Ready Products</li>
                            <li>✔️ Barcode Scanning Support</li>
                            <li>✔️ Automated Integrated Website</li>
                        </ul>
                    </div>
                </div>
            </div>

        </main>

        <div x-show="openModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition>
            <div @click.away="openModal = false" class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="bg-slate-900 text-white p-4 font-bold flex justify-between items-center">
                    <span>Provision System Infrastructure</span>
                    <button @click="openModal = false" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                
                <form action="{{ route('tenants.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Company / Retail Shop Name</label>
                        <input type="text" name="company_name" required placeholder="e.g., Rahim Super Shop" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Desired Isolated Subdomain</label>
                        <div class="flex">
                            <input type="text" name="subdomain" required placeholder="rahim" class="w-full border border-gray-300 rounded-l-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none text-right font-bold text-indigo-600">
                            <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg p-2.5 text-xs text-gray-500 font-bold flex items-center">
                                .{{ config('tenancy.central_domains')[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4 mt-6">
                        <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow transition">Launch Instance 🚀</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>