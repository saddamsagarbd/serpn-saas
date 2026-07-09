<aside class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm">
    <div class="p-5 border-b border-gray-100 flex items-center gap-3 bg-white">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">S</div>
        <span class="text-lg font-extrabold text-slate-800 tracking-tight">SERPN <span class="text-blue-600 font-semibold text-sm">SAAS</span></span>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 mb-2">Main</p>
            <nav class="space-y-1">
                <a href="{{ route('tenant.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i data-lucide="{{ "layout-dashboard" }}" class="w-4 h-4"></i>
                    <span>{{ "Dashboard" }}</span>
                </a>
            </nav>
        </div>

        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 mb-2">User Management</p>
            <nav class="space-y-1 px-2">
                {{-- Top-level links: Dashboard, Profile, Settings --}}
                @foreach(config('menu.default_features') as $link)
                    <a href="{{ route($link['route']) }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs($link['route']) ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i data-lucide="{{ $link['icon'] }}" class="w-4 h-4"></i>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach

                <div class="pt-2 mt-2 border-t border-gray-100"></div>

                {{-- Feature-wise dropdown menus --}}
                @foreach(config('menu.menus') as $key => $menu)
                    @if(hasFeature($key))
                        <div x-data="{ open: {{ request()->is($key.'/*') || request()->is($key) ? 'true' : 'false' }} }" class="select-none">
                            
                            <!-- 📂 Level 1: Main Menu Button (e.g., Inventory) -->
                            <button @click="open = !open"
                                    type="button"
                                    class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                                <span class="flex items-center gap-3">
                                    <i data-lucide="{{ $menu['icon'] }}" class="w-4 h-4"></i>
                                    <span>{{ $menu['label'] }}</span>
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Level 1 Wrapper -->
                            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                                @foreach($menu['items'] as $item)
                                    
                                    @if(isset($item['sub'])) 
                                        <!-- 🛠️ Level 2: Nested Sub-Group (e.g., Item Master) -->
                                        <div x-data="{ subOpen: {{ request()->is($key.'/categories*') || request()->is($key.'/units*') || request()->is($key.'/products*') ? 'true' : 'false' }} }" class="space-y-1">
                                            <button @click="subOpen = !subOpen" 
                                                    type="button"
                                                    class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                                                <span class="flex items-center gap-3">
                                                    <i data-lucide="{{ $item['sub']['icon'] ?? '' }}" class="w-4 h-4 text-slate-400"></i>
                                                    <span>{{ $item['sub']['label'] }}</span>
                                                </span>
                                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="subOpen ? 'rotate-180' : ''"></i>
                                            </button>
                                            
                                            <div x-show="subOpen" x-collapse class="border-l-2 border-slate-100 pl-4 space-y-1">
                                                @foreach($item['sub']['items'] as $subItem)
                                                    <a href="{{ route($subItem['route']) }}"
                                                    class="block px-3 py-1.5 ml-8 text-sm text-slate-500 hover:text-slate-900 transition-colors {{ request()->routeIs($subItem['route']) ? 'text-blue-600 font-medium' : '' }}">
                                                        {{ $subItem['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <!-- 📄 Level 2: Standard Single Link (Suppliers, PO, GRN) -->
                                        <a href="{{ route($item['route']) }}"
                                        class="block px-3 py-1.5 rounded-lg text-sm text-slate-500 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                                            <span class="flex items-center gap-3">
                                                <i data-lucide="{{ $item['icon'] ?? '' }}" class="w-4 h-4 text-slate-400"></i>
                                                <span>{{ $item['label'] }}</span>
                                            </span>
                                        </a>
                                    @endif

                                @endforeach
                            </div>

                        </div>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
    
    <div class="p-4 border-t border-gray-100 text-[11px] text-gray-400 text-center font-medium bg-gray-50">
        v1.0.0 © 2026 SERPN SYSTEM
    </div>
</aside>