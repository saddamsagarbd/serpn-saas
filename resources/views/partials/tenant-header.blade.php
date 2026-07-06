<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Welcome Back, {{ auth()->user()->name ?? 'Anonymous' }}!
    </h1>

    <div class="flex items-center gap-4">
        <!-- Tenant Badge -->
        <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
            🏢 {{ tenant('company_name') }}
        </span>

        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>

                <div class="text-left hidden sm:block">
                    <p class="text-sm font-medium text-gray-800">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown -->
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border z-50"
            >
                <div class="px-4 py-3 border-b">
                    <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500 truncate">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <a
                    href="#"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    👤 Profile
                </a>

                <a
                    href="#"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    ⚙️ Settings
                </a>

                <div class="border-t"></div>

                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                        🚪 Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>