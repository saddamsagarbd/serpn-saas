<aside class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm">
    <div class="p-5 border-b border-gray-100 flex items-center gap-3 bg-white">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">S</div>
        <span class="text-lg font-extrabold text-slate-800 tracking-tight">SERPN <span class="text-blue-600 font-semibold text-sm">SAAS</span></span>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 mb-2">Main</p>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-base">📊</span> Dashboard
                </a>
            </nav>
        </div>

        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 mb-2">User Management</p>
            <nav class="space-y-1">
                <a href="{{ route('plans') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('plans') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-base">🏪</span> Plans
                </a>
                <a href="{{ route('tenants') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('tenants') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-base">🏪</span> Tenants / Vendors
                </a>
            </nav>
        </div>
    </div>
    
    <div class="p-4 border-t border-gray-100 text-[11px] text-gray-400 text-center font-medium bg-gray-50">
        v1.0.0 © 2026 SERPN SYSTEM
    </div>
</aside>