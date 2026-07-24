<div x-show="openModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-md flex items-center justify-center z-50 p-4 animate-fade-in" x-cloak>
    <div @click.away="openModal = false" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">

        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 text-white p-5 font-semibold text-base flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏢</span>
                <div>
                    <h3 class="font-bold tracking-tight" x-text="isEdit ? 'Edit Business Node' : 'Provision New Business Node'"></h3>
                    <p class="text-[10px] text-indigo-200 font-normal mt-0.5">Isolated MySQL database and localized routing engine map.</p>
                </div>
            </div>
            <button type="button" @click="openModal = false" class="text-indigo-200 hover:text-white text-2xl transition-colors">&times;</button>
        </div>

        <!-- Submitted via saveTenant() in the parent Alpine scope (fetch/JSON) —
             NOT a native form POST, so this stays inside the SPA and never
             navigates away or fights the parent's tenants[] state. -->
        <div class="p-6 space-y-5 text-xs overflow-y-auto">

            <template x-if="formErrors && formErrors.length">
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex gap-3 items-start animate-shake">
                    <span class="text-base mt-0.5">⚠️</span>
                    <div>
                        <p class="font-bold mb-1 text-sm">Action Required:</p>
                        <ul class="list-disc pl-4 space-y-0.5 font-medium text-rose-600">
                            <template x-for="err in formErrors" :key="err">
                                <li x-text="err"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>

            <div class="space-y-1.5">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Company Name</label>
                <input type="text" x-model="tenantData.company_name" required placeholder="e.g. House 57 or Rahim Bazar"
                    class="w-full border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
            </div>

            <div class="space-y-1.5">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Subdomain</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" x-model="tenantData.subdomain" required placeholder="rahim-bazar"
                        class="w-full border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 text-sm font-mono focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                    <span class="text-slate-400 font-mono whitespace-nowrap">.yourapp.com</span>
                </div>
            </div>

            <!-- Business Type — values match menu.php resolver: garment | real_estate | manufacturing | general_retail -->
            <div class="space-y-1.5 border-t border-slate-100 pt-4">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Business Type</label>
                <div class="relative">
                    <select x-model="tenantData.business_type" required class="w-full appearance-none border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 pr-10 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs cursor-pointer">
                        <option value="" disabled>Select a business type</option>
                        <template x-for="type in businessTypes" :key="type.value">
                            <option :value="type.value" x-text="type.label"></option>
                        </template>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 font-bold text-sm">▼</div>
                </div>
                <p class="text-[11px] text-slate-400">Determines which vertical modules (Merchandising, Properties, Production, etc.) this tenant sees.</p>
            </div>

            <!-- Subscription Plan — separate field from business type, was previously colliding on name="plan_id" -->
            <div class="space-y-1.5 border-t border-slate-100 pt-4">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Assign ERP Subscription Plan</label>
                <div class="relative">
                    <select x-model="tenantData.plan" required class="w-full appearance-none border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 pr-10 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs cursor-pointer">
                        <option value="" disabled>Select a business package</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">
                                📦 {{ $plan->title }} — ৳{{ number_format($plan->price, 0) }} / {{ ucfirst($plan->billing_period) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 font-bold text-sm">▼</div>
                </div>
            </div>

            <!-- Trial duration — its own field, no longer colliding with plan_id -->
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-600 tracking-wide uppercase">Trial Period</label>
                <div class="relative">
                    <select x-model="tenantData.trial_months" class="w-full appearance-none border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 pr-10 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs cursor-pointer">
                        <option value="0">No trial</option>
                        <option value="1">1 month</option>
                        <option value="3">3 months</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 font-bold text-sm">▼</div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 space-y-3.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-slate-400">🔑</span>
                    <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Root Vendor Admin Account</h4>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-600 tracking-wide uppercase">Owner / Primary Admin Name</label>
                    <input type="text" x-model="tenantData.owner_name" required placeholder="e.g. Md. Rahim"
                        class="w-full border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-600 tracking-wide uppercase">Admin Login Email</label>
                        <input type="email" x-model="tenantData.owner_email" required placeholder="rahim@gmail.com"
                            class="w-full border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-600 tracking-wide uppercase">Contact Phone Number</label>
                        <input type="text" x-model="tenantData.owner_phone" required placeholder="017XXXXXXXX"
                            class="w-full border border-slate-200 focus:ring-indigo-500 bg-slate-50/50 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all shadow-2xs">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 border-t border-slate-100 pt-4 mt-6 text-xs font-bold bg-white sticky bottom-0">
                <button type="button" @click="openModal = false" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all active:scale-98">Cancel</button>
                <button type="button" @click="saveTenant()" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl shadow-md hover:shadow-indigo-200 hover:shadow-lg transition-all flex items-center gap-2 active:scale-98">
                    <span>🚀</span> <span x-text="isEdit ? 'Update & Notify' : 'Save & Notify Vendor Admin'"></span>
                </button>
            </div>
        </div>
    </div>
</div>