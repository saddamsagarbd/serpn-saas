<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 z-10 shadow-sm">
    <div class="flex items-center gap-4">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">🔍</span>
            <input type="text" placeholder="Menu search..." class="pl-9 pr-4 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white w-60 transition-all">
        </div>
    </div>
    
    <div class="flex items-center gap-4 text-gray-500 text-sm font-medium">
        <span class="cursor-pointer hover:text-gray-800 transition">📅 {{ date('d-F-Y') }}</span>
        <span class="cursor-pointer hover:text-gray-800 transition relative">🔔<span class="w-2 h-2 bg-red-500 rounded-full absolute top-0 right-0 animate-pulse"></span></span>
        <div class="border-l h-5 border-gray-200 mx-1"></div>
        <span class="text-gray-700 font-semibold text-xs bg-slate-100 px-3 py-1.5 rounded-full">Super Admin Console</span>
    </div>
</header>