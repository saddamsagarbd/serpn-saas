<div x-show="openModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
    x-transition
    style="display: none;">
     
    <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full shadow-xl overflow-hidden" @click.away="openModal = false">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide" x-text="isEdit ? 'Modify Unit Settings' : 'Register New Unit Master'"></h3>
            <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="saveUnit()" class="p-6 space-y-4">
            @csrf
            
            {{-- ১. ইউনিটের নাম --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unit Name *</label>
                <input type="text" x-model="unitData.name" required placeholder="e.g. Kilogram, Box, Piece" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800">
            </div>

            {{-- ২. ইউনিটের সংক্ষিপ্ত কোড বা শর্ট নেম --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Short Name / Code *</label>
                <input type="text" x-model="unitData.short_name" required placeholder="e.g. KG, Box, Pcs" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800 font-mono uppercase">
            </div>

            {{-- ৩. টাইপ চয়েস (বেজ ইউনিট কি না) --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Is Base Unit? *</label>
                <select x-model="unitData.is_base_unit" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                    <option value="1">Yes (This is a standalone primary unit)</option>
                    <option value="0">No (This is a sub/conversion unit)</option>
                </select>
            </div>

            {{-- 🛠️ ইআরপি কনভার্সন প্যানেল: শুধু মাত্র 'is_base_unit == 0' (No) হলে এটি স্লাইডিং অ্যানিমেশনে ওপেন হবে --}}
            <div x-show="unitData.is_base_unit == '0'" 
                 x-collapse 
                 class="space-y-4 p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
                
                <span class="text-[10px] font-black text-indigo-600 uppercase block tracking-wider mb-2">⚡ ERP Unit Conversion Configuration</span>

                {{-- বেজ ইউনিট সিলেক্ট --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Parent Base Unit</label>
                    <select x-model="unitData.base_unit_id" :required="unitData.is_base_unit == '0'" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                        <option value="">-- Select Parent Base Unit --</option>
                        
                        {{-- 💡 ম্যাজিক পরিবর্তন: ব্লেড লুপের বদলে আমরা আমাদের রিয়েল-টাইম units অ্যারে থেকে ফিল্টার করে লুপ চালাচ্ছি --}}
                        <template x-for="base in units" :key="base.id">
                            {{-- কন্ডিশন ১: শুধুমাত্র মেইন বেজ ইউনিটগুলোই ড্রপডাউনে দেখাবে (is_base_unit == 1) --}}
                            {{-- কন্ডিশন ২: এডিট করার সময় একটি ইউনিট যাতে নিজেকেই নিজের প্যারেন্ট না বানিয়ে ফেলে (base.id != unitData.id) --}}
                            <template x-if="base.is_base_unit == 1 && base.id != unitData.id">
                                <option :value="base.id" x-text="base.name + ' (' + base.short_name + ')'"></option>
                            </template>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- অপারেটর গুণ নাকি ভাগ --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Mathematical Operator</label>
                        <select x-model="unitData.operator" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium font-mono">
                            <option value="*">Multiply (*) [Standard]</option>
                            <option value="/">Divide (/)</option>
                        </select>
                    </div>

                    {{-- ভ্যালু কত হবে (যেমন ১২ পিস হলে ১২) --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Operator Value</label>
                        <input type="number" step="0.0001" x-model="unitData.operator_value" :required="unitData.is_base_unit == '0'" placeholder="e.g. 12.00" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-bold text-slate-800 font-mono">
                    </div>
                </div>

                {{-- লাইভ রিলেশন প্রিভিউ মেসেজ --}}
                <div class="text-[10px] text-indigo-500 font-medium italic mt-1">
                    Preview: 1 <span class="font-bold uppercase" x-text="unitData.short_name || 'SubUnit'"></span> = <span class="font-bold" x-text="unitData.operator_value || '1'"></span> <span x-text="unitData.operator"></span> (Selected Parent Unit)
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition" x-text="isEdit ? 'Update Changes' : 'Save Unit'"></button>
            </div>
        </form>
    </div>
</div>