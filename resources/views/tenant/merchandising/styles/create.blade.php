@extends('layouts.tenant')
@section('title', 'Create Style Master & Costing')

@push('styles')
<style>
    /* Select2 Tailwind Override */
    .select2-container--default .select2-selection--single {
        border-color: #cbd5e1 !important;
        border-radius: 0.5rem !important;
        height: 38px !important;
        padding-top: 3px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 0.75rem !important;
        color: #0f172a !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    /* Hide Number Input Spinners */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endpush

@section('content')

<div x-data="styleCreationApp({{ json_encode([
    'isEdit' => isset($style),
    'id' => isset($style) ? $style->id : null,
    'style_code' => isset($style) ? $style->style_number : '',
    'style_name' => isset($style) ? $style->product_name : '',
    'buyer_id' => isset($style) ? $style->buyer_id : '',
    'season_id' => isset($style) ? $style->season_id : '',
    'target_price' => isset($style) && $style->costing ? $style->costing->target_fob : '',
    'currency' => isset($style) && $style->costing ? $style->costing->currency : 'USD',
    'revenue_percent' => isset($style) && $style->costing ? $style->costing->revenue_percent : 6.00,
    'ait_percent' => isset($style) && $style->costing ? $style->costing->ait_percent : 5.00,
    'vat_percent' => isset($style) && $style->costing ? $style->costing->vat_percent : 10.00,
    'services' => isset($style) && $style->costing ? [
        'print_cost' => $style->costing->print_cost,
        'emb_cost' => $style->costing->emb_cost,
        'wash_cost' => $style->costing->wash_cost,
        'cm_cost' => $style->costing->cm_cost,
        'overhead_cost' => $style->costing->overhead_cost
    ] : [
        'print_cost' => 0.00,
        'emb_cost' => 0.00,
        'wash_cost' => 0.00,
        'cm_cost' => 0.00,
        'overhead_cost' => 0.00
    ],
    'items' => isset($style) && $style->costing && $style->costing->bomItems->count() > 0 
        ? $style->costing->bomItems->map(function($item) {
            return [
                'item_id' => $item->item_id ?? '',
                'item_name' => $item->item_description,
                'item_type' => strtolower($item->category) === 'fabric' ? 'fabric' : 'trim',
                'color_id' => $item->color_id ?? '',
                'size_id' => $item->size_id ?? '',
                'qty' => $item->consumption,
                'wastage' => $item->wastage_percent,
                'cost' => $item->unit_price
            ];
          }) 
        : [['item_id' => '', 'item_name' => '', 'item_type' => 'fabric', 'color_id' => '', 'size_id' => '', 'qty' => '', 'wastage' => '', 'cost' => '']]
]) }})" class="bg-slate-100 min-h-screen p-2 sm:p-4 space-y-4">

    <!-- Top Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h4 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Modify Style Master & Costing' : 'New Style & Costing Sheet'"></h4>
            <p class="text-xs text-slate-500 mt-0.5">Define core parameters, BOM materials, and live POS FOB pricing calculation.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.merch.styles') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">Cancel</a>
            <button type="button" @click="submitForm" :disabled="isSaving" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-400 rounded-xl shadow-md transition-all flex items-center gap-2">
                <span x-text="isSaving ? 'Saving Sheet...' : 'Save Style & Costing'"></span>
            </button>
        </div>
    </div>

    <form @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        <template x-if="isEdit">
            <input type="hidden" name="_method" value="PUT">
        </template>

        <!-- LEFT SIDE: Style Info & Material BOM (8 Cols / Large 9 Cols) -->
        <div class="lg:col-span-8 xl:col-span-9 space-y-6">
            
            <!-- SECTION 1: Style Header Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h5 class="text-xs font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> 1. Style Basic Master Data
                </h5>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Style Code <span class="text-red-500">*</span></label>
                        <input type="text" x-model="styleCode" placeholder="e.g., H57-TS-001" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold font-mono focus:bg-white focus:outline-none focus:border-indigo-500 transition-all" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Style Name/Desc <span class="text-red-500">*</span></label>
                        <input type="text" x-model="styleName" placeholder="e.g., Mens Tee" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-medium focus:bg-white focus:outline-none focus:border-indigo-500 transition-all" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Buyer <span class="text-red-500">*</span></label>
                        <select x-model="buyerId" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all" required>
                            <option value="">-- Select Buyer --</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Season <span class="text-red-500">*</span></label>
                        <select x-model="seasonId" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all" required>
                            <option value="">-- Select Season --</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}">{{ $season->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Target Price (<span x-text="currencySymbol"></span>)</label>
                        <input type="number" step="0.0001" x-model.number="targetPrice" placeholder="0.0000" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold font-mono focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <!-- Image Upload Component -->
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase block">Style Image (JPG, PNG)</label>
                        <div x-show="!imagePreview">
                            <input type="file" 
                                x-ref="fileInput"
                                @change="handleImageUpload($event)" 
                                accept=".jpg,.jpeg,.png" 
                                class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all file:mr-3 file:py-0.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>

                        <div x-show="imagePreview" class="relative inline-block mt-1">
                            <img :src="imagePreview" class="h-16 w-16 object-cover rounded-xl border border-slate-300 shadow-sm">
                            <button type="button" 
                                    @click="removeImage" 
                                    class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full p-1 shadow-md transition-all transform hover:scale-110 flex items-center justify-center"
                                    title="Delete Image">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: BOM Materials Grid Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h5 class="text-xs font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> 2. Material BOM Items (Fabric & Trims)
                    </h5>
                    <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 px-3.5 py-1.5 rounded-xl border border-indigo-200/80 hover:bg-indigo-100 transition-all">
                        + Add Component
                    </button>
                </div>

                <div class="border border-slate-200/80 rounded-2xl overflow-x-auto shadow-xs">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="p-3 pl-4 w-3/12">Item Description</th> 
                                <th class="p-3 w-2/12">Color</th>
                                <th class="p-3 w-2/12">Size</th>
                                <th class="p-3 w-1.5/12 text-right">Consumption</th>
                                <th class="p-3 w-1.5/12 text-center">Wastage %</th>
                                <th class="p-3 w-1.5/12 text-right">Unit Cost (<span x-text="currencySymbol"></span>)</th>
                                <th class="p-3 w-2/12 text-right">Total Cost (<span x-text="currencySymbol"></span>)</th>
                                <th class="p-3 w-1/12 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-700 font-medium">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50/50 transition-all">
                                    <td class="p-2 pl-4">
                                        <select 
                                            x-init="initSelect2($el, item)" 
                                            class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none">
                                            <option value="">Search Item...</option>
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <select x-model="item.color_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                            <option value="">Select</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <select x-model="item.size_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                            <option value="">Select</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" min="0" placeholder="0.00" x-model.number="item.qty" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-indigo-600 focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" placeholder="5" x-model.number="item.wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.0001" min="0" placeholder="0.0000" x-model.number="item.cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="text" :value="((item.qty || 0) * (item.cost || 0) * (1 + ((item.wastage || 0) / 100))).toFixed(4)" readonly class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800">
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 font-bold text-base transition-all">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-800 text-xs">
                                <td colspan="6" class="p-3 pl-4 text-right text-[10px] uppercase text-slate-500">Total Material Cost</td>
                                <td class="p-3 text-right font-mono text-emerald-600 text-sm font-extrabold" x-text="currencySymbol + ' ' + grandTotal"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: POS Checkout Style Sidebar (4 Cols / Large 3 Cols) -->
        <div class="lg:col-span-4 xl:col-span-3 space-y-6 lg:sticky lg:top-4">
            
            <!-- Value Addition Services Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <h5 class="text-xs font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Making Charges & Services
                </h5>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Print Cost</label>
                        <input type="number" step="0.0001" min="0" x-model.number="services.print_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Embroidery Cost</label>
                        <input type="number" step="0.0001" min="0" x-model.number="services.emb_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Wash Cost</label>
                        <input type="number" step="0.0001" min="0" x-model.number="services.wash_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">CM Cost</label>
                        <input type="number" step="0.0001" min="0" x-model.number="services.cm_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Overhead Cost</label>
                        <input type="number" step="0.0001" min="0" x-model.number="services.overhead_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between font-bold text-slate-800">
                        <span>Total Services</span>
                        <span class="font-mono text-indigo-600 text-sm" x-text="currencySymbol + ' ' + totalServiceCost.toFixed(4)"></span>
                    </div>
                </div>
            </div>

            <!-- POS Summary & Markups Dark Panel -->
            <div class="bg-slate-900 text-white rounded-2xl border border-slate-800 shadow-xl p-5 space-y-4">
                <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800 pb-3 flex items-center justify-between">
                    <span>Summary & Markups</span>
                    <span class="text-[10px] text-indigo-400 font-mono bg-indigo-950 px-2 py-0.5 rounded-md border border-indigo-800">LIVE POS</span>
                </h5>

                <!-- Subtotals -->
                <div class="space-y-2 text-xs border-b border-slate-800 pb-3">
                    <div class="flex justify-between items-center text-slate-400">
                        <span>Total Material Cost</span>
                        <span class="font-mono text-slate-200 font-bold" x-text="currencySymbol + ' ' + grandTotal"></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>Value Add & Services</span>
                        <span class="font-mono text-slate-200 font-bold" x-text="currencySymbol + ' ' + totalServiceCost.toFixed(4)"></span>
                    </div>
                    <div class="flex justify-between items-center pt-1 text-amber-400 font-bold">
                        <span>Base Cost (TTL Cost)</span>
                        <span class="font-mono text-sm" x-text="currencySymbol + ' ' + totalBaseCost.toFixed(4)"></span>
                    </div>
                </div>

                <!-- Markups Inputs -->
                <div class="space-y-3">
                    <!-- Revenue Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-300 uppercase">
                            <span>REVENUE (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + revenueAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" step="0.01" min="0" x-model.number="revenuePercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-800 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
                    </div>

                    <!-- AIT Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-300 uppercase">
                            <span>AIT (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + aitAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" step="0.01" min="0" x-model.number="aitPercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-800 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
                    </div>

                    <!-- VAT Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-300 uppercase">
                            <span>VAT (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + vatAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" step="0.01" min="0" x-model.number="vatPercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-800 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                </div>

                <!-- Total FOB & Final Offered Price -->
                <div class="pt-3 border-t border-slate-800 space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Calculated TTL FOB:</span>
                        <span class="font-mono font-bold text-emerald-400 text-base" x-text="currencySymbol + ' ' + calculatedFobPrice.toFixed(2)"></span>
                    </div>

                    <div class="bg-indigo-950/80 rounded-2xl p-4 border border-indigo-500/30 text-center space-y-0.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-300">Offered Price (Rounded)</span>
                        <div class="font-mono text-3xl font-black text-indigo-400" x-text="currencySymbol + ' ' + offeredPrice"></div>
                    </div>
                </div>

                <!-- Save Action Button in POS Panel -->
                <button type="submit" :disabled="isSaving" class="w-full py-3 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-700 rounded-xl shadow-lg transition-all text-center uppercase tracking-wider cursor-pointer">
                    <span x-text="isSaving ? 'Processing...' : 'Complete & Save Style'"></span>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function styleCreationApp(initialData) {
    return {
        isEdit: initialData.isEdit,
        styleId: initialData.id,
        styleCode: initialData.style_code,
        styleName: initialData.style_name,
        buyerId: initialData.buyer_id,
        seasonId: initialData.season_id,
        targetPrice: initialData.target_price,
        imagePreview: initialData.image ? '/storage/' + initialData.image : null,
        imageFile: null,
        currency: initialData.currency,
        currencySymbol: (initialData.currency === 'TAKA' || initialData.currency === 'BDT') ? '৳' : '$',
        
        revenuePercent: initialData.revenue_percent || 6.00,
        aitPercent: initialData.ait_percent || 5.00,
        vatPercent: initialData.vat_percent || 10.00,

        services: initialData.services,
        isSaving: false,
        items: initialData.items,

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPG, JPEG, and PNG images are allowed!');
                    event.target.value = '';
                    return;
                }

                this.imageFile = file;
                this.imagePreview = URL.createObjectURL(file);
            }
        },

        removeImage() {
            this.imagePreview = null;
            this.imageFile = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        initSelect2(el, item) {
            this.$nextTick(() => {
                const $el = $(el);

                if (typeof $el.select2 !== 'function') {
                    console.error('Select2 is not loaded on jQuery object.');
                    return;
                }

                $el.select2({
                    placeholder: "Search Item...",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('tenant.api.item_masters.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term || '' };
                        },
                        processResults: function (data) {
                            return { results: data.results };
                        },
                        cache: true
                    },
                });

                if (item.item_id && item.item_name) {
                    let option = new Option(item.item_name, item.item_id, true, true);
                    $el.append(option).trigger('change');
                }

                $el.on('select2:select', (e) => {
                    const selectedData = e.params.data;
                    item.item_id = selectedData.id;
                    item.item_name = selectedData.name || selectedData.text;
                    if (selectedData.item_type) {
                        item.item_type = selectedData.item_type;
                    }
                });

                $el.on('select2:unselect', () => {
                    item.item_id = '';
                    item.item_name = '';
                });
            });
        },

        addItem() {
            this.items.push({ item_id: '', item_name: '', item_type: 'fabric', color_id: '', size_id: '', qty: '', cost: '' });
        },

        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        get grandTotal() {

            const total = this.items.reduce((sum, item) => {
                const qty = parseFloat(item.qty) || 0;
                const cost = parseFloat(item.cost) || 0;
                const wastage = parseFloat(item.wastage) || 0;
                
                const itemTotal = (qty * cost) * (1 + (wastage / 100));
                return sum + itemTotal;
            }, 0);
            
            return total.toFixed(4);
        },

        get totalServiceCost() {
            return (parseFloat(this.services.print_cost) || 0) +
                   (parseFloat(this.services.emb_cost) || 0) +
                   (parseFloat(this.services.wash_cost) || 0) +
                   (parseFloat(this.services.cm_cost) || 0) +
                   (parseFloat(this.services.overhead_cost) || 0);
        },

        get totalBaseCost() {
            return parseFloat(this.grandTotal) + this.totalServiceCost;
        },

        get revenueAmount() {
            return this.totalBaseCost * ((parseFloat(this.revenuePercent) || 0) / 100);
        },

        get totalWithRevenue() {
            return this.totalBaseCost + this.revenueAmount;
        },

        get aitAmount() {
            return this.totalWithRevenue * ((parseFloat(this.aitPercent) || 0) / 100);
        },

        get totalWithAIT() {
            return this.totalWithRevenue + this.aitAmount;
        },

        get vatAmount() {
            return this.totalWithAIT * ((parseFloat(this.vatPercent) || 0) / 100);
        },

        get calculatedFobPrice() {
            return this.totalWithAIT + this.vatAmount;
        },

        get offeredPrice() {
            return this.calculatedFobPrice.toFixed(2);
        },

        submitForm() {
            if (!this.styleCode.trim() || !this.styleName.trim() || !this.buyerId || !this.seasonId) {
                alert("Please complete all required fields (*).");
                return;
            }

            this.isSaving = true;

            let url = this.isEdit 
                ? "{{ route('tenant.merch.styles.update', ['id' => '__id']) }}".replace('__id', this.styleId)
                : "{{ route('tenant.merch.styles.store') }}";

            let formData = new FormData();
            formData.append('style_code', this.styleCode);
            formData.append('style_name', this.styleName);
            formData.append('buyer_id', this.buyerId);
            formData.append('season_id', this.seasonId);
            formData.append('target_price', this.targetPrice || 0);
            formData.append('currency', this.currency);
            formData.append('revenue_percent', this.revenuePercent);
            formData.append('ait_percent', this.aitPercent);
            formData.append('vat_percent', this.vatPercent);
            formData.append('calculated_fob', this.calculatedFobPrice);
            formData.append('offered_price', this.offeredPrice);

            if (this.imageFile) {
                formData.append('image', this.imageFile);
            }

            Object.keys(this.services).forEach(key => {
                formData.append(`services[${key}]`, this.services[key] || 0);
            });

            this.items.forEach((item, index) => {
                formData.append(`items[${index}][item_id]`, item.item_id || '');
                formData.append(`items[${index}][item_name]`, item.item_name || '');
                formData.append(`items[${index}][item_type]`, item.item_type || '');
                formData.append(`items[${index}][color_id]`, item.color_id || '');
                formData.append(`items[${index}][size_id]`, item.size_id || '');
                formData.append(`items[${index}][qty]`, item.qty || 0);
                formData.append(`items[${index}][cost]`, item.cost || 0);
                formData.append(`items[${index}][wastage]`, item.wastage || 0);
            });

            if (this.isEdit) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        let errorMessages = Object.values(data.errors).flat().join("\n");
                        alert("Validation Failed:\n" + errorMessages);
                    } else {
                        alert("Server Error: " + (data.message || "Something went wrong."));
                    }
                    return null;
                }
                return data;
            })
            .then(data => {
                if (!data) return;
                this.isSaving = false;
                if (data.success) {
                    if (typeof toastr !== 'undefined') toastr.success(data.message || "Saved successfully.");
                    window.location.href = "{{ route('tenant.merch.styles') }}";
                } else {
                    alert("Execution Error: " + data.message);
                }
            })
            .catch(error => {
                this.isSaving = false;
                console.error(error);
                alert("A genuine network or transport layer error occurred.");
            });
        }
    };
}
</script>
@endpush