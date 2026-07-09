<div x-show="openModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
    x-transition
    style="display: none;"> {{-- ইনিশিয়াল ফ্লিকারিং এড়াতে style এড করা ভালো --}}
    
    <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full shadow-xl overflow-hidden" @click.away="openModal = false">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide" x-text="isEdit ? 'Modify Category Settings' : 'Register New Category'"></h3>
            <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        {{-- 🛠️ পরিবর্তন: @submit.prevent যুক্ত করা হয়েছে এবং ডাইনামিক :action রিমুভ করা হয়েছে কারণ আমরা Fetch দিয়ে হ্যান্ডেল করছি --}}
        <form @submit.prevent="saveCategory()" class="p-6 space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Category Title *</label>
                <input type="text" name="name" x-model="categoryData.name" required placeholder="e.g. Executive Chairs" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Parent Assignment Hierarchy</label>
                <select name="parent_id" x-model="categoryData.parent_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
                    <option value="">-- No Parent (Keep as Root Level) --</option>
                    @foreach($parentCategories as $parent)
                        {{-- নিজের ক্যাটাগরি নিজেই যাতে প্যারেন্ট না হতে পারে তার জন্য টেমপ্লেট কন্ডিশন ঠিক আছে --}}
                        <template x-if="categoryData.id != '{{ $parent->id }}'">
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        </template>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Description Context</label>
                <textarea name="description" x-model="categoryData.description" rows="3" placeholder="Optional category structural notes..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-800"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition" x-text="isEdit ? 'Update Changes' : 'Save Category'"></button>
            </div>
        </form>
    </div>
</div>