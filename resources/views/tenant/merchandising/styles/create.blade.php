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
    'product_image' => isset($style) && $style->product_image ? $style->product_image : '',
    'currency' => isset($style) && $style->costing ? $style->costing->currency : 'USD',
    'revenue_percent' => isset($style) && $style->costing ? $style->costing->revenue_percent : 6.00,
    'ait_percent' => isset($style) && $style->costing ? $style->costing->ait_percent : 5.00,
    'vat_percent' => isset($style) && $style->costing ? $style->costing->vat_percent : 10.00,
    'services' => isset($style) && $style->costing ? [
        'print_cost' => $style->costing->print_cost,
        'print_wastage' => $style->costing->print_wastage ?? 0,
        'emb_cost' => $style->costing->emb_cost,
        'emb_wastage' => $style->costing->emb_wastage ?? 0,
        'wash_cost' => $style->costing->wash_cost,
        'wash_wastage' => $style->costing->wash_wastage ?? 0,
        'cm_cost' => $style->costing->cm_cost,
        'cm_wastage' => $style->costing->cm_wastage ?? 0,
        'overhead_cost' => $style->costing->overhead_cost,
        'overhead_wastage' => $style->costing->overhead_wastage ?? 0,
    ] : [
        'print_cost' => '',
        'print_wastage' => '',
        'emb_cost' => '',
        'emb_wastage' => '',
        'wash_cost' => '',
        'wash_wastage' => '',
        'cm_cost' => '',
        'cm_wastage' => '',
        'overhead_cost' => '',
        'overhead_wastage' => '',
    ],
    'items' => isset($style) && $style->costing && $style->costing->bomItems->count() > 0 
        ? $style->costing->bomItems->map(function($item) {
            return [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'cat_id' => $item->category_id ?? '',
                'cat_name' => $item->category_name ?? '',
                'item_id' => $item->item_id ?? '',
                'item_name' => $item->item_description,
                'item_type' => strtolower($item->category) === 'fabrics' ? 'fabrics' : 'trim',
                'color_id' => $item->color_id ?? '',
                'size_id' => $item->size_id ?? '',
                'qty' => $item->consumption,
                'wastage' => $item->wastage_percent,
                'cost' => $item->unit_price
            ];
        }) 
        : [[
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'cat_id' => '', 
            'cat_name' => '', 
            'item_id' => '', 
            'item_name' => '', 
            'item_type' => 'fabrics', 
            'color_id' => '', 
            'size_id' => '', 
            'qty' => '', 
            'wastage' => '', 
            'cost' => ''
        ]]
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
                        <label class="text-[11px] font-bold text-slate-600 uppercase">FOB (<span x-text="currencySymbol"></span>)</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)" x-model.number="targetPrice" placeholder="0.0000" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold font-mono focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
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
                            <img :src="formattedImage" class="h-16 w-16 object-cover rounded-xl border border-slate-300 shadow-sm">
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
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Currency</label>
                        <select x-model="currency" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="BDT">BDT (৳)</option>
                        </select>
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

                <div class="border border-slate-200/80 rounded-2xl overflow-visible shadow-xs">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="p-3 pl-4 w-3/12">Category</th> 
                                <th class="p-3 pl-4 w-3/12">Item</th> 
                                <!-- <th class="p-3 w-2/12">Color</th>
                                <th class="p-3 w-2/12">Size</th> -->
                                <th class="p-3 w-1.5/12 text-right">Consumption</th>
                                <th class="p-3 w-1.5/12 text-center">Wastage %</th>
                                <th class="p-3 w-1.5/12 text-right">Unit Cost (<span x-text="currencySymbol"></span>)</th>
                                <th class="p-3 w-2/12 text-right">Total Cost (<span x-text="currencySymbol"></span>)</th>
                                <th class="p-3 w-1/12 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-700 font-medium">
                            <template x-for="(item, index) in items" :key="item.id">
                                <tr class="hover:bg-slate-50/50 transition-all">
                                    <td class="p-2 pl-4" x-data="categorySearchBox(item)" @click.outside="open = false">
                                        <div class="relative">
                                            <input type="text"
                                                x-model="query"
                                                x-effect="query = item.cat_name"
                                                @input="search()"
                                                @focus="if (results.length) open = true"
                                                @keydown.escape="open = false"
                                                placeholder="Search Category..."
                                                autocomplete="off"
                                                class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">

                                            <div x-show="open" x-cloak
                                                class="absolute left-0 top-full mt-1 w-72 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl text-xs z-50">
                                                <template x-if="loading">
                                                    <div class="p-2 text-slate-400">Searching...</div>
                                                </template>
                                                <template x-if="!loading && results.length === 0 && query.length > 0">
                                                    <div class="p-2 text-slate-400">No matches</div>
                                                </template>
                                                <template x-for="r in results" :key="r.id">
                                                    <div @click="select(r)"
                                                        class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700">
                                                        <span x-text="r.text"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-2 pl-4" x-data="itemSearchBox(item)" @click.outside="open = false">
                                        <div class="relative">
                                            <input type="text"
                                                x-model="query"
                                                @input="search()"
                                                @focus="if (results.length) open = true"
                                                @keydown.escape="open = false"
                                                placeholder="Search Item..."
                                                autocomplete="off"
                                                class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">

                                            <div x-show="open" x-cloak
                                                class="absolute left-0 top-full mt-1 w-72 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl text-xs z-50">
                                                <template x-if="loading">
                                                    <div class="p-2 text-slate-400">Searching...</div>
                                                </template>
                                                <template x-if="!loading && results.length === 0 && query.length > 0">
                                                    <div class="p-2 text-slate-400">No matches</div>
                                                </template>
                                                <template x-for="r in results" :key="r.id">
                                                    <div @click="select(r)"
                                                        class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700">
                                                        <span x-text="r.text"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td class="p-2">
                                        <select x-model="item.color_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                            <option value="">Select</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </td> -->
                                    <!-- <td class="p-2">
                                        <select x-model="item.size_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                            <option value="">Select</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </td> -->
                                    <td class="p-2">
                                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.00" x-model.number="item.qty" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-indigo-600 focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  step="0.01" placeholder="5" x-model.number="item.wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="item.cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-indigo-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="text" :value="((item.qty || 0) * (item.cost || 0) * (1 + ((item.wastage || 0) / 100))).toFixed(2)" readonly class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800">
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
            <!-- <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <h5 class="text-xs font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Making Charges & Services
                </h5>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Print Cost</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="services.print_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Embroidery Cost</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="services.emb_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Wash Cost</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="services.wash_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">CM Cost</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="services.cm_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-600 uppercase">Overhead Cost</label>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="services.overhead_cost" placeholder="0.0000" class="w-28 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-right font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between font-bold text-slate-800">
                        <span>Total Services</span>
                        <span class="font-mono text-indigo-600 text-sm" x-text="currencySymbol + ' ' + totalServiceCost.toFixed(2)"></span>
                    </div>
                </div>
            </div> -->

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <h5 class="text-xs font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Making Charges & Services
                </h5>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="p-3 pl-4 w-4/12">Service</th>
                                <th class="p-3 w-2/12 text-center">Wastage %</th>
                                <th class="p-3 w-3/12 text-right">Unit Cost (<span x-text="currencySymbol"></span>)</th>
                                <th class="p-3 w-3/12 text-right pr-4">Total Cost (<span x-text="currencySymbol"></span>)</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-700 font-medium">
                            <!-- Print Cost -->
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-2 pl-4">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase">Print</label>
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0" x-model.number="services.print_wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="services.print_cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2 pr-4 text-right font-mono font-bold text-slate-800">
                                    <span x-text="((parseFloat(services.print_cost) || 0) * (1 + ((parseFloat(services.print_wastage) || 0) / 100))).toFixed(2)"></span>
                                </td>
                            </tr>

                            <!-- Embroidery Cost -->
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-2 pl-4">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase">Embroidery</label>
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0" x-model.number="services.emb_wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="services.emb_cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2 pr-4 text-right font-mono font-bold text-slate-800">
                                    <span x-text="((parseFloat(services.emb_cost) || 0) * (1 + ((parseFloat(services.emb_wastage) || 0) / 100))).toFixed(2)"></span>
                                </td>
                            </tr>

                            <!-- Wash Cost -->
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-2 pl-4">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase">Wash</label>
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0" x-model.number="services.wash_wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="services.wash_cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2 pr-4 text-right font-mono font-bold text-slate-800">
                                    <span x-text="((parseFloat(services.wash_cost) || 0) * (1 + ((parseFloat(services.wash_wastage) || 0) / 100))).toFixed(2)"></span>
                                </td>
                            </tr>

                            <!-- CM Cost -->
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-2 pl-4">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase">CM</label>
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0" x-model.number="services.cm_wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="services.cm_cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2 pr-4 text-right font-mono font-bold text-slate-800">
                                    <span x-text="((parseFloat(services.cm_cost) || 0) * (1 + ((parseFloat(services.cm_wastage) || 0) / 100))).toFixed(2)"></span>
                                </td>
                            </tr>

                            <!-- Overhead Cost -->
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-2 pl-4">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase">Overhead</label>
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0" x-model.number="services.overhead_wastage" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-center font-mono focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="number" onkeydown="preventInvalidNumberInput(event)"  placeholder="0.0000" x-model.number="services.overhead_cost" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="p-2 pr-4 text-right font-mono font-bold text-slate-800">
                                    <span x-text="((parseFloat(services.overhead_cost) || 0) * (1 + ((parseFloat(services.overhead_wastage) || 0) / 100))).toFixed(2)"></span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-800 text-xs">
                                <td colspan="2" class="p-3 pl-4 text-right text-[10px] uppercase text-slate-500">Total Services Cost</td>
                                <td colspan="2" class="p-2 pr-4 text-right font-mono text-indigo-600 text-sm font-extrabold" x-text="currencySymbol + ' ' + totalServiceCost.toFixed(2)"></td>
                            </tr>
                        </tfoot>
                    </table>
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
                        <span class="font-mono text-slate-200 font-bold" x-text="currencySymbol + ' ' + totalServiceCost.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center pt-1 text-amber-400 font-bold">
                        <span>Base Cost (TTL Cost)</span>
                        <span class="font-mono text-sm" x-text="currencySymbol + ' ' + totalBaseCost.toFixed(2)"></span>
                    </div>
                </div>

                <!-- Markups Inputs -->
                <div class="space-y-3">
                    <!-- Revenue Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-200 uppercase">
                            <span>REVENUE (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + revenueAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="revenuePercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
                    </div>

                    <!-- AIT Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-200 uppercase">
                            <span>AIT (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + aitAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="aitPercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
                    </div>

                    <!-- VAT Row -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] font-bold text-slate-200 uppercase">
                            <span>VAT (%)</span>
                            <span class="font-mono text-indigo-400" x-text="currencySymbol + ' ' + vatAmount.toFixed(2)"></span>
                        </div>
                        <input type="number" onkeydown="preventInvalidNumberInput(event)"  x-model.number="vatPercent" 
                            class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 font-mono font-bold text-right focus:outline-none focus:border-indigo-500 text-xs">
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
        imagePreview: initialData.product_image ? '/storage/' + initialData.product_image : null,
        imageFile: null,
        currency: initialData.currency,
        // currencySymbol: (initialData.currency === 'TAKA' || initialData.currency === 'BDT') ? '৳' : '$',
        currencySymbol: '',
        
        revenuePercent: initialData.revenue_percent || 6.00,
        aitPercent: initialData.ait_percent || 5.00,
        vatPercent: initialData.vat_percent || 10.00,

        services: initialData.services,
        isSaving: false,
        items: initialData.items,

        init() {
            const symbols = { BDT: '৳', TAKA: '৳', EUR: '€', USD: '$' };
            this.$watch('currency', value => {
                const upper = (value || '').toUpperCase();
                this.currencySymbol = symbols[upper] || '$';
            });
            
            // Set initial symbol
            this.currencySymbol = ((initialData.currency || '').toUpperCase() === 'TAKA' || (initialData.currency || '').toUpperCase() === 'BDT') ? '৳' : symbols[initialData.currency];
        },

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



        addItem() {
            this.items.push({ 
                id        : (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random().toString(36).slice(2)),
                cat_id    : '',
                cat_name  : '',
                item_id   : '',
                item_name : '',
                item_type : 'fabrics',
                // color_id  : '',
                // size_id   : '',
                qty       : '',
                cost      : ''
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                // Destroy Select2 on row element before removing from DOM
                const $tableRows = $(this.$el).find('tbody tr');
                const $targetSelect = $tableRows.eq(index).find('select').first();
                if ($targetSelect.hasClass('select2-hidden-accessible')) {
                    $targetSelect.select2('destroy');
                }
                this.items.splice(index, 1);
            }
        },

        get grandTotal() {
            const total = this.items.reduce((sum, item) => {
                const qty = parseFloat(item.qty) || 0;
                const cost = parseFloat(item.cost) || 0;
                const wastage = parseFloat(item.wastage) || 0;
                
                const itemTotal = (qty * cost) * (1 + (wastage / 100));
                return sum + itemTotal;
            }, 0);
            
            return total.toFixed(2);
        },

        get totalServiceCost() {
            const calcCost = (cost, wastage) => {
                const c = parseFloat(cost) || 0;
                const w = parseFloat(wastage) || 0;
                return c * (1 + (w / 100));
            };

            return calcCost(this.services.print_cost, this.services.print_wastage)
                + calcCost(this.services.emb_cost, this.services.emb_wastage)
                + calcCost(this.services.wash_cost, this.services.wash_wastage)
                + calcCost(this.services.cm_cost, this.services.cm_wastage)
                + calcCost(this.services.overhead_cost, this.services.overhead_wastage);
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

        get formattedImage() {
            if (!this.imagePreview) return '';
            
            // Blob/Data URL হলে সরাসরি দেখাবে
            if (this.imagePreview.startsWith('blob:') || this.imagePreview.startsWith('data:')) {
                return this.imagePreview;
            }
            
            // পাথ যদি /storage/ দিয়ে শুরু না হয়, তবে যোগ করবে
            const cleanPath = this.imagePreview.replace(/^\/+/, '');
            return cleanPath.startsWith('storage/') ? '/' + cleanPath : '/storage/' + cleanPath;
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
                formData.append(`items[${index}][cat_id]`, item.cat_id || '');
                formData.append(`items[${index}][cat_name]`, item.cat_name || '');
                formData.append(`items[${index}][item_id]`, item.item_id || '');
                formData.append(`items[${index}][item_name]`, item.item_name || '');
                formData.append(`items[${index}][item_type]`, item.item_type || '');
                // formData.append(`items[${index}][color_id]`, item.color_id || '');
                // formData.append(`items[${index}][size_id]`, item.size_id || '');
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

function itemSearchBox(item) {
    return {
        query: item.item_name || '',
        results: [],
        open: false,
        loading: false,
        _timer: null,
        _controller: null,
        _seq: 0,

        search() {
            item.item_id = '';
            item.item_name = this.query;

            clearTimeout(this._timer);
            if (!this.query.trim()) {
                this.results = [];
                this.open = false;
                return;
            }

            this._timer = setTimeout(() => this.fetchResults(), 250);
        },

        async fetchResults() {
            if (this._controller) this._controller.abort();
            this._controller = new AbortController();
            const seq = ++this._seq;

            this.loading = true;

            const params = new URLSearchParams({
                q: this.query,
                cat_id: item.cat_id || ''
            });

            try {
                const res = await fetch(
                    "{{ route('tenant.api.item_masters.search') }}?" + params.toString(),
                    { signal: this._controller.signal, headers: { 'Accept': 'application/json' } }
                );
                const data = await res.json();

                if (seq !== this._seq) return; // stale response, ignore

                this.results = data.results || [];
                this.open = true;
            } catch (e) {
                if (e.name !== 'AbortError') console.error(e);
            } finally {
                if (seq === this._seq) this.loading = false;
            }
        },

        select(r) {
            item.item_id = r.id;
            item.item_name = r.name || r.text;
            if (r.item_type) item.item_type = r.item_type;

            if (r.cat_id && !item.cat_id) {
                item.cat_id = r.cat_id;
                item.cat_name = r.cat_name || r.category_name || '';
            }

            this.query = r.name || r.text;
            this.results = [];
            this.open = false;
        }
    };
}

function categorySearchBox(item) {
    return {
        query: item.cat_name || '',
        results: [],
        open: false,
        loading: false,
        _timer: null,
        _controller: null,
        _seq: 0,

        search() {
            item.cat_id = '';
            item.cat_name = this.query;

            clearTimeout(this._timer);
            if (!this.query.trim()) {
                this.results = [];
                this.open = false;
                return;
            }

            this._timer = setTimeout(() => this.fetchResults(), 250);
        },

        async fetchResults() {
            if (this._controller) this._controller.abort();
            this._controller = new AbortController();
            const seq = ++this._seq;

            this.loading = true;
            try {
                const res = await fetch(
                    "{{ route('tenant.api.category_masters.search') }}?q=" + encodeURIComponent(this.query),
                    { signal: this._controller.signal, headers: { 'Accept': 'application/json' } }
                );
                const data = await res.json();

                if (seq !== this._seq) return; // stale response, ignore

                this.results = data.results || [];
                this.open = true;
            } catch (e) {
                if (e.name !== 'AbortError') console.error(e);
            } finally {
                if (seq === this._seq) this.loading = false;
            }
        },

        select(r) {
            item.cat_id = r.id;
            item.cat_name = r.name || r.text;
            this.query = r.name || r.text;
            this.results = [];
            this.open = false;
        }
    };
}

function preventInvalidNumberInput(event) {
    // e, E, +, - এবং ই-মেইল/অন্যান্য চিহ্ন ব্লক করার জন্য
    const invalidKeys = ['e', 'E', '+', '-'];
    
    if (invalidKeys.includes(event.key)) {
        event.preventDefault();
    }
}

</script>
@endpush