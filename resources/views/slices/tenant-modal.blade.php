<div x-show="openModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-md flex items-center justify-center z-50 p-4 animate-fade-in" x-cloak>
    <div @click.away="openModal = false" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
        
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 text-white p-5 font-semibold text-base flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏢</span>
                <div>
                    <h3 class="font-bold tracking-tight">Provision New Business Node</h3>
                    <p class="text-[10px] text-indigo-200 font-normal mt-0.5">Isolated MySQL database and localized routing engine map.</p>
                </div>
            </div>
            <button type="button" @click="openModal = false" class="text-indigo-200 hover:text-white text-2xl transition-colors">&times;</button>
        </div>

        <form action="{{ route('tenants.store') }}" method="POST" class="p-6 space-y-5 text-xs overflow-y-auto">
            @csrf

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex gap-3 items-start animate-shake">
                    <span class="text-base mt-0.5">⚠️</span>
                    <div>
                        <p class="font-bold mb-1 text-sm">Action Required:</p>
                        <ul class="list-disc pl-4 space-y-0.5 font-medium text-rose-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-1 gap-5">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required placeholder="e.g. House 57 or Rahim Bazar" 
                        class="w-full border @error('company_name') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                </div>

                <!-- <div class="space-y-1.5" x-data="{ sub: '{{ old('company_domain', '') }}' }">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Workspace Address</label>
                    <div class="flex items-center shadow-2xs group">
                        <div class="relative flex-1">
                            <input type="text" name="company_domain" x-model="sub" required placeholder="rahim-bazar" 
                                class="w-full border @error('company_domain') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-l-xl p-3 pr-2 text-sm font-semibold font-mono tracking-wide text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all">
                        </div>
                        <span class="bg-slate-100 border border-l-0 @error('company_domain') border-rose-400 @else border-slate-200 @enderror px-3.5 py-3 rounded-r-xl font-bold text-slate-500 text-sm tracking-tight select-none bg-linear-to-b from-slate-50 to-slate-100">
                            .{{ config('tenancy.central_domains')[0] ?? 'serpn-saas.test' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-1.5 mt-2 px-1">
                        <span class="w-1.5 h-1.5 rounded-full" :class="sub.trim() ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"></span>
                        <p class="text-[10px] font-medium text-slate-400">
                            Live Node Target: 
                            <span class="font-bold font-mono text-slate-600" x-text="sub.trim() ? sub.toLowerCase().replace(/[^a-z0-9-]/g, '') + '.{{ config('tenancy.central_domains')[0] ?? 'serpn-saas.test' }}' : 'waiting for input...'"></span>
                        </p>
                    </div>
                </div> -->
            </div>

            <div class="space-y-1.5 border-t border-slate-100 pt-4">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Assign ERP Subscription Plan</label>
                <div class="relative">
                    <select name="plan_id" required class="w-full appearance-none border @error('plan_id') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 pr-10 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs cursor-pointer">
                        <option value="" disabled selected class="text-slate-400">Select a business package</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                📦 {{ $plan->title }} — ৳{{ number_format($plan->price, 0) }} / {{ ucfirst($plan->billing_period) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 font-bold text-sm">
                        ▼
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-5">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Trial for</label>
                    <select name="plan_id" required class="w-full appearance-none border @error('plan_id') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 pr-10 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs cursor-pointer">
                        <option value="1" class="text-slate-400">1 months</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 font-bold text-sm">
                        ▼
                    </div>
                </div>

                <!-- <div class="space-y-1.5" x-data="{ sub: '{{ old('company_domain', '') }}' }">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Workspace Address</label>
                    <div class="flex items-center shadow-2xs group">
                        <div class="relative flex-1">
                            <input type="text" name="company_domain" x-model="sub" required placeholder="rahim-bazar" 
                                class="w-full border @error('company_domain') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-l-xl p-3 pr-2 text-sm font-semibold font-mono tracking-wide text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all">
                        </div>
                        <span class="bg-slate-100 border border-l-0 @error('company_domain') border-rose-400 @else border-slate-200 @enderror px-3.5 py-3 rounded-r-xl font-bold text-slate-500 text-sm tracking-tight select-none bg-linear-to-b from-slate-50 to-slate-100">
                            .{{ config('tenancy.central_domains')[0] ?? 'serpn-saas.test' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-1.5 mt-2 px-1">
                        <span class="w-1.5 h-1.5 rounded-full" :class="sub.trim() ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"></span>
                        <p class="text-[10px] font-medium text-slate-400">
                            Live Node Target: 
                            <span class="font-bold font-mono text-slate-600" x-text="sub.trim() ? sub.toLowerCase().replace(/[^a-z0-9-]/g, '') + '.{{ config('tenancy.central_domains')[0] ?? 'serpn-saas.test' }}' : 'waiting for input...'"></span>
                        </p>
                    </div>
                </div> -->
            </div>

            <div class="border-t border-slate-100 pt-4 space-y-3.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-slate-400">🔑</span>
                    <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Root Vendor Admin Account</h4>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Owner / Primary Admin Name</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required placeholder="e.g. Md. Rahim" 
                        class="w-full border @error('owner_name') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-600 tracking-wide uppercase">Admin Login Email</label>
                        <input type="email" name="owner_email" value="{{ old('owner_email') }}" required placeholder="rahim@gmail.com" 
                            class="w-full border @error('owner_email') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-600 tracking-wide uppercase">Contact Phone Number</label>
                        <input type="text" name="owner_phone" value="{{ old('owner_phone') }}" required placeholder="017XXXXXXXX" 
                            class="w-full border @error('owner_phone') border-rose-400 focus:ring-rose-500 @else border-slate-200 focus:ring-indigo-500 @enderror bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 border-t border-slate-100 pt-4 mt-6 text-xs font-bold bg-white sticky bottom-0">
                <button type="button" @click="openModal = false" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all active:scale-98">Cancel</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl shadow-md hover:shadow-indigo-200 hover:shadow-lg transition-all flex items-center gap-2 active:scale-98">
                    <span>🚀</span> Save & Notify Vendor Admin
                </button>
            </div>
        </form>
    </div>
</div>