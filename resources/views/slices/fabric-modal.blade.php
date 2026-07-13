<div x-show="openModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
    x-transition
    style="display: none;">
    
    <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full shadow-xl overflow-hidden" @click.away="openModal = false">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide" x-text="isEdit ? 'Modify Fabric Specification Settings' : 'Register New Fabric Spec'"></h3>
            <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="saveFabric()" class="p-6 space-y-4">
            @csrf
            
            {{-- ১. ইউনিটের নাম --}}
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Name *</label>
                <input type="text" x-model="fabricData.name" required placeholder="e.g. 20x16 Twill (98% Cotton 2% Spandex), Poly Kotet" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">gsm (optional)</label>
                <input type="text" x-model="fabricData.gsm" placeholder="e.g. 275 gsm" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800">
            </div>          
            
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition" x-text="isEdit ? 'Update Changes' : 'Save'"></button>
            </div>
        </form>
    </div>
</div>