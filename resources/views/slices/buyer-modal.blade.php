
    <div 
        x-show="openModal" 
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50" 
        x-cloak>
        <div x-ref="modalContainer" class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden" @click.away="openModal = false">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800">Add New Buyer</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form @submit.prevent="submitForm" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Buyer Name *</label>
                    <input type="text" x-model="formData.name" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., H&M, Zara" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Country</label>
                    <div class="relative">
                        <select x-model="formData.country" class="w-full px-3 py-2 text-xs border rounded-xl bg-white focus:outline-none focus:border-indigo-500 appearance-none pr-8">
                            <option value="">Select a country</option>
                            @foreach($countryList as $country)
                                @if(is_array($country) && isset($country['id']))
                                    <option value="{{ $country['id'] }}">
                                        {{ $country['name'] ?? 'Unknown' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <!-- Custom clean chevron arrow for style since we used appearance-none -->
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Contact Person</label>
                    <input type="text" x-model="formData.contact_person" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="e.g., John Doe">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Email Address</label>
                    <input type="email" x-model="formData.email" class="w-full px-3 py-2 text-xs border rounded-xl focus:outline-none focus:border-indigo-500" placeholder="buyer@example.com">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Save Buyer</button>
                </div>
            </form>
        </div>
    </div>