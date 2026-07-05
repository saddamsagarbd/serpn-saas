<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Welcome Back, {{ auth()->user()->name ?? "Annonymous" }}!</h1>
    <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
        🏢 {{ tenant('company_name') }}
    </span>
</div>