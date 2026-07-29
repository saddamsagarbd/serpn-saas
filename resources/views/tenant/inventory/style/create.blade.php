@extends('layouts.tenant')
@section('title', 'Create Style Master')
@section('content')

<div x-data="styleCreationApp()" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- টপবার -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">New Style Creation Wizard</h4>
            <p class="text-xs text-slate-400 mt-0.5">Define your core style master parameters and constituent components.</p>
        </div>
    </div>

    <form @submit.prevent="submitForm" class="p-6 space-y-5">
        @csrf
        <!-- ইনফরমেশন মেটা ব্লক (Header Info) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Style Code *</label>
                <input type="text" x-model="styleCode" placeholder="e.g., H57-TS-001" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 font-bold font-mono focus:outline-none focus:border-indigo-500" required>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Style Name/Desc *</label>
                <input type="text" x-model="styleName" placeholder="e.g., Ladies Denim Jacket" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:border-indigo-500" required>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Buyer *</label>
                <select x-model="buyerId" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                    <option value="">Select Buyer</option>
                    @foreach($buyers as $buyer)
                        <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Season *</label>
                <select x-model="seasonId" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                    <option value="">Select Season</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target Price (<span x-text="currencySymbol"></span>)</label>
                <input type="number" step="0.0001" x-model.number="targetPrice" placeholder="0.0000" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 font-bold font-mono focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <!-- বাল্ক উপাদান/উপাদান গ্রিড টেবিল (Fabric & Trims Grid) -->
        <div class="space-y-3">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Initial BOM Components / Item Lines</h5>
            <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-3/12">Component Item</th>
                            <th class="p-3 w-1.5/12">Item Type</th>
                            <th class="p-3 w-1.5/12">Color Context</th>
                            <th class="p-3 w-1.5/12">Size Chart</th>
                            <th class="p-3 w-1.5/12 text-right">Consumption</th>
                            <!-- <th class="p-3 w-1.5/12 text-right">Unit</th> -->
                            <th class="p-3 w-1.5/12 text-right">Unit Price (<span x-text="currencySymbol"></span>)</th>
                            <th class="p-3 w-1.5/12 text-right">Total Cost (<span x-text="currencySymbol"></span>)</th>
                            <th class="p-3 w-0.5/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="p-2.5 pl-4">
                                    <input type="text" x-model="item.item_name" placeholder="Shell Fabric, Button..." class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5">
                                    <select x-model="item.item_type" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="fabric">Fabric</option>
                                        <option value="trim">Trims / Accessories</option>
                                    </select>
                                </td>
                                <td class="p-2.5">
                                    <select x-model="item.color_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2.5">
                                    <select x-model="item.size_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Size Chart</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2.5">
                                    <input type="number" step="0.01" min="0" placeholder="0.00" x-model.number="item.qty" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                </td>
                                <!-- <td class="p-2.5">
                                    <select x-model="item.size_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Size Chart</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td> -->
                                <td class="p-2.5">
                                    <input type="number" step="0.0001" min="0" placeholder="0.0000" x-model.number="item.cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2.5">
                                    <input type="text" :value="((item.qty || 0) * (item.cost || 0)).toFixed(4)" readonly class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-500">
                                </td>
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-700 text-xs">
                            <td colspan="4" class="p-3 pl-4 text-right text-[10px] uppercase tracking-wider text-slate-400">Total Material Est (<span x-text="currencySymbol"></span>)</td>
                            <td class="p-3 text-right font-mono text-indigo-600" x-text="grandQty.toFixed(2)"></td>
                            <td></td>
                            <td class="p-3 text-right font-mono text-emerald-600" x-text="grandTotal"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 bg-indigo-50/50 px-3 py-2 rounded-xl border border-dashed border-indigo-200 hover:bg-indigo-50 transition">
                + Add Component Line
            </button>
        </div>

        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('tenant.inventory.styles') }}" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</a>
            <button type="submit" :disabled="isSaving" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-400 rounded-xl shadow-sm transition">
                <span x-text="isSaving ? 'Saving Master...' : 'Save Style Master'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function styleCreationApp() {
    return {
        styleCode: '',
        styleName: '',
        buyerId: '',
        seasonId: '',
        targetPrice: '',
        currency: 'USD', // <-- Add this default state
        currencySymbol: '$', // <-- Add this helper state
        isSaving: false,
        items: [
            { item_name: '', item_type: 'fabric', color_id: '', size_id: '', qty: '', cost: '' }
        ],
        updateCurrency(val) {
            this.currency = val;
            this.currencySymbol = (val === 'TAKA' || val === 'BDT') ? '৳' : '$';
        },

        addItem() {
            this.items.push({ item_name: '', item_type: 'fabric', color_id: '', size_id: '', qty: '', cost: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        get grandQty() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.qty) || 0), 0);
        },
        get grandTotal() {
            const total = this.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.cost) || 0)), 0);
            return total.toFixed(4);
        },

        submitForm() {
            if (!this.styleCode.trim() || !this.styleName.trim() || !this.buyerId || !this.seasonId) {
                alert("Please complete all required fields (*).");
                return;
            }

            this.isSaving = true;

            fetch("{{ route('tenant.inventory.styles.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    style_code: this.styleCode,
                    style_name: this.styleName,
                    buyer_id: this.buyerId,
                    season_id: this.seasonId,
                    target_price: this.targetPrice,
                    items: this.items
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                this.isSaving = false;
                if (data.success) {
                    toastr.success(data.message || "Style master data loaded perfectly.")
                    window.location.href = "{{ route('tenant.inventory.styles') }}";
                } else {
                    alert("Execution Error: " + data.message);
                }
            })
            .catch(error => {
                this.isSaving = false;
                alert("Transport layer connection error occurred.");
            });
        }
    }
}
</script>
@endpush