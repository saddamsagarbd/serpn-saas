<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 z-10 shadow-sm">
    <!-- Left -->
    <div class="flex items-center gap-4">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">🔍</span>
            <input
                type="text"
                placeholder="Menu search..."
                class="pl-9 pr-4 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white w-60 transition-all"
            >
        </div>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-5">

        <span class="text-sm text-gray-500">
            📅 {{ now()->format('d F Y') }}
        </span>

        <button class="relative text-xl text-gray-500 hover:text-gray-700">
            🔔
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        </button>

        <span class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-full">
            Super Admin Console
        </span>

        <!-- Profile Dropdown -->
        <div
            x-data="{ open: false }"
            class="relative"
        >
            <button
                @click="open = !open"
                class="flex items-center gap-3 focus:outline-none"
            >
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase">
                    {{ substr(auth()->user()->name,0,1) }}
                </div>

                <div class="hidden md:block text-left">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown -->
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-3 w-56 bg-white rounded-xl border border-gray-200 shadow-xl overflow-hidden z-50"
            >
                <div class="px-4 py-3 border-b">
                    <h4 class="font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </h4>

                    <p class="text-xs text-gray-500 truncate">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <a
                    href="#"
                    class="flex items-center gap-2 px-4 py-3 text-sm hover:bg-gray-50"
                >
                    👤 Profile
                </a>

                <a
                    href="#"
                    class="flex items-center gap-2 px-4 py-3 text-sm hover:bg-gray-50"
                >
                    ⚙️ Settings
                </a>

                <div class="border-t"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                    >
                        🚪 Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>