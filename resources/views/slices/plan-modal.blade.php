<!-- MODAL SECTION -->
<div x-show="openModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4" x-transition x-cloak>
    <div @click.away="openModal = false" class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="bg-slate-900 text-white p-4 font-bold text-sm flex justify-between items-center">
            <span>Configure SaaS Subscription Package</span>
            <button @click="openModal = false" class="text-gray-400 hover:text-white text-lg">&times;</button>
        </div>
        
        <form action="{{ route('plans.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg">
                    <p class="font-bold mb-1">দয়া করে নিচের ভুলগুলো ঠিক করুন:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Plan Title / Name</label>
                    <input type="text" name="plan[title]" required placeholder="e.g., Business Pro" class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Plan Code (Unique Identifier)</label>
                    <input type="text" name="plan[code]" required placeholder="e.g., business_pro" class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 font-mono focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Price (BDT)</label>
                    <input type="number" name="plan[price]" required placeholder="3500" class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Billing Period</label>
                    <select name="plan[billing_period]" class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                        <option value="month">Monthly</option>
                        <option value="year">Yearly</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-3">
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Max Product Limit</label>
                    <input 
                    type="number" 
                    name="plan[max_product_limit]" 
                    min="-1" 
                    value="-1" 
                    required 
                    @input="if ($el.value !== '' && parseInt($el.value) < -1) { $el.value = -1; }"
                    @keydown="
                        if (['e', 'E', '+', '.'].includes($event.key)) { $event.preventDefault(); }
                        if ($event.key === '-' && $el.value.length > 0) { $event.preventDefault(); }
                    "
                    class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">unlimited এর জন্য -1 লিখুন</p>
                </div>
                <div>
                    <label class="block font-bold text-gray-500 uppercase mb-1">Max Invoice Limit (Per Month)</label>
                    <input 
                    type="number" 
                    name="plan[max_invoice_limit]" 
                    min="-1" 
                    value="-1" 
                    required 
                    @input="if ($el.value !== '' && parseInt($el.value) < -1) { $el.value = -1; }"
                    @keydown="
                        if (['e', 'E', '+', '.'].includes($event.key)) { $event.preventDefault(); }
                        if ($event.key === '-' && $el.value.length > 0) { $event.preventDefault(); }
                    " 
                    class="w-full border border-gray-200 bg-slate-50/50 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:bg-white focus:outline-none transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">unlimited এর জন্য -1 লিখুন</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="plan[best_deal]" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="block font-bold text-gray-500 uppercase mb-1">Best Deal</span>
                </label>
            </div>

            <div class="border-t border-gray-50 pt-3">
                <label class="block font-bold text-gray-400 uppercase tracking-wider mb-2">Enable Core Modules</label>
                <div class="space-y-2 bg-slate-50 p-3 rounded-xl border border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="features[default_features]" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700">Default Features</span>
                    </label>
                    @foreach(config('saas.selectable_features') as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="features[{{ $key }}]" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="font-medium text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-6">
                <button type="button" @click="openModal = false" class="px-4 py-2 font-semibold text-gray-500 hover:bg-slate-50 rounded-lg transition">Dismiss</button>
                <button type="submit" class="px-5 py-2 font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md transition">Publish Package 🚀</button>
            </div>
        </form>
    </div>
</div>