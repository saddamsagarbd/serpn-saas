<div 
    x-show="openModal" 
    class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50" 
    x-cloak>
    <div x-ref="modalContainer" class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden" @click.away="openModal = false">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-slate-800">Company Info</h3>
            <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase">Company Name *</label>
                <input type="text" x-model="formData.name" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., House 57" required>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase">Company Code *</label>
                <input type="text" x-model="formData.code" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., House 57" required>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase">Company Address</label>
                <input type="text" x-model="formData.address" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., Uttara, Dhaka" required>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 rounded-xl">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>