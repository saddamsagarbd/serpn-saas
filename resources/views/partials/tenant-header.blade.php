<header class="bg-white border-b border-slate-100 px-6 py-3 flex items-center justify-between w-full">
    
    <div class="flex items-center space-x-4">
        <div class="inline-flex items-center gap-1.5 bg-indigo-50/80 px-3 py-1.5 rounded-xl border border-indigo-100/50">
            <span class="text-base">🏢</span>
            <span class="text-xs font-bold text-indigo-700 tracking-wide">
                {{ tenant('company_name') }}
            </span>
        </div>
    </div>

    <div class="flex items-center">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 p-1.5 hover:bg-slate-50 rounded-xl transition border border-transparent hover:border-slate-100 focus:outline-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr bg-blue-600 from-indigo-600 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="hidden md:block text-left whitespace-nowrap">
                    <p class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">{{ auth()->user()->email }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" 
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-lg py-1 z-50">
                <a href="#" class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">My Profile</a>
                <a href="#" class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Company Settings</a>
                <hr class="border-slate-100 my-1">
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button class="block px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">Logout</button>
                </form>
            </div>
        </div>
    </div>

</header>