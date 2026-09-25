@extends('layouts.tenant')
@section('title', isset($bom) ? 'Edit Production BOM' : 'Create Production BOM')

@push('styles')
<style>
    /* Select2 / Tailwind Custom Overrides */
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
<div x-data="productionBomApp(
        {{ json_encode($styles) }}, 
        {{ json_encode($selectedStyleId) }}, 
        {{ json_encode($selectedMprId) }}, 
        {{ json_encode($selectedMpr) }}, 
        {{ json_encode($bom ?? null) }}
    )" 
    class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Edit Production BOM & Cost Sheet' : 'New Production BOM & Cost Sheet'"></h4>
            <p class="text-xs text-slate-400 mt-0.5">Calculate raw materials, trims, printing, embroidery, washing, and overhead cost sheets based on MPR order matrix.</p>
        </div>
    </div>

    <form @submit.prevent="submitBom" class="p-6 space-y-6">
        @csrf

        <!-- 1. Header Selection Context -->
        <div class="space-y-4">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">1. Select Style & Linked MPR Order</h5>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
                <!-- Select Master Style -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select Style *</label>
                    <select x-model="selectedStyleId" @change="onStyleChange()" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                        <option value="">-- Choose Style --</option>
                        <template x-for="style in availableStyles" :key="style.id">
                            <option :value="style.id" x-text="(style.style_code || style.style_number) + ' - ' + (style.style_name || '')"></option>
                        </template>
                    </select>
                </div>

                <!-- Buyer Name Display -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Buyer Context</label>
                    <input type="text" :value="buyerName" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-medium cursor-not-allowed" placeholder="Auto-populated">
                </div>

                <!-- Select MPR / Sales Order -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select MPR / Sales Order *</label>
                    <select x-model="selectedMprId" @change="onMprChange()" :disabled="!selectedStyleId || loadingMprs" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100" required>
                        <option value="">-- Choose MPR Order --</option>
                        <template x-for="mpr in mprOrders" :key="mpr.id">
                            <option :value="mpr.id" x-text="(mpr.buyer_po_number || mpr.order_number || ('MPR #' + mpr.id)) + ' (Total Qty: ' + mprTotalQty(mpr) + ' Pcs)'"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Cost Sheet Line Items Breakdown -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">2. Cost Breakdown (Materials & Processing Services)</h5>
                
                <div class="flex items-center gap-2">
                    <button type="button" @click="addBomRow('Material')" :disabled="!selectedMprId" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition disabled:opacity-50">
                        + Add Material Line
                    </button>
                    <button type="button" @click="addBomRow('Processing')" :disabled="!selectedMprId" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition disabled:opacity-50">
                        + Add Process Line (Print/Emb/Wash)
                    </button>
                </div>
            </div>

            <div class="border border-slate-200/80 rounded-xl overflow-visible shadow-sm bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-2/12">Category / Cost Head</th>
                            <th class="p-3 w-3/12">Item / Processing Description</th>
                            <th class="p-3 w-3/12">MPR Color / Size Matrix</th>
                            <th class="p-3 w-1/12 text-right">Garment Qty</th>
                            <th class="p-3 w-1/12 text-right">Consumption (Per GMT)</th>
                            <th class="p-3 w-1/12 text-right">Total Req. Qty</th>
                            <th class="p-3 w-1/12 text-right">Unit Price ($)</th>
                            <th class="p-3 w-1/12 text-right">Total Cost ($)</th>
                            <th class="p-3 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <template x-for="(row, index) in items" :key="index">
                            <tr :class="row.cost_type === 'Processing' ? 'bg-emerald-50/20' : ''">
                                
                                <!-- Cost Head / Category Selector -->
                                <td class="p-2 pl-4" x-data="categorySearchBox(row)" @click.outside="open = false">
    
                                    <!-- 1. Processing Services Dropdown (Visible when row.cost_type === 'Processing') -->
                                    <template x-if="row.cost_type === 'Processing'">
                                        <div class="space-y-1">
                                            <select x-model="row.cost_head" 
                                                @change="row.cat_name = row.cost_head" 
                                                class="w-full p-1.5 text-xs bg-emerald-50/50 border border-emerald-200 rounded-lg focus:outline-none focus:border-emerald-500 font-semibold text-emerald-800">
                                                <optgroup label="Services & Processing">
                                                    <option value="Print Cost">Printing Service</option>
                                                    <option value="Embroidery Cost">Embroidery Service</option>
                                                    <option value="Wash Cost">Washing Service</option>
                                                    <option value="Special Process">Special Treatment / Dyeing</option>
                                                    <option value="Testing & Inspection">Lab Testing / Inspection</option>
                                                    <option value="CM / Overhead">CM / Operational Cost</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </template>

                                    <!-- 2. On-the-fly Category Live Search Input (Visible for Standard Materials) -->
                                    <template x-if="row.cost_type !== 'Processing'">
                                        <div class="relative">
                                            <input type="text"
                                                x-model="query"
                                                x-effect="query = row.cat_name || ''"
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
                                                    <div class="p-2 text-slate-400">No matches found</div>
                                                </template>
                                                <template x-for="r in results" :key="r.id">
                                                    <div @click="select(r)"
                                                        class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700">
                                                        <span x-text="r.name || r.text"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                </td>

                                <!-- Costing Item / Service Description Search -->
                                <td class="p-2" x-data="itemSearchBox(row, $data)" @click.outside="open = false">
                                    <!-- 1. If Processing (Print/Embroidery/Wash), load options directly from Style Costing / Options -->
                                    <template x-if="row.cost_type === 'Processing'">
                                        <div class="space-y-1">
                                            <select x-model="row.bom_item_id" 
                                                @change="onProcessSelect(row)" 
                                                class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 font-medium text-slate-800">
                                                <option value="">-- Select Process / Work --</option>
                                                <template x-for="proc in getStyleProcesses(row.cost_head)" :key="proc.id">
                                                    <option :value="proc.id" 
                                                        :selected="row.bom_item_id == proc.id"
                                                        x-text="proc.name + (proc.rate ? ' ($' + proc.rate + ')' : '')">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="row.cost_type !== 'Processing'">
                                        <div class="relative">
                                            <input type="text"
                                                x-model="query"
                                                @input="search()"
                                                @focus="if (results.length) open = true"
                                                @keydown.escape="open = false"
                                                :placeholder="row.cost_type === 'Processing' ? 'e.g. Chest Rubber Print, Garment Wash...' : 'Search Material...'"
                                                autocomplete="off"
                                                class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">

                                            <div x-show="open" x-cloak
                                                class="absolute left-0 top-full mt-1 w-72 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl text-xs z-50">
                                                <template x-if="loading">
                                                    <div class="p-2 text-slate-400">Searching...</div>
                                                </template>
                                                <template x-if="!loading && results.length === 0 && query.length > 0">
                                                    <div class="p-2 text-slate-400">No matches found</div>
                                                </template>
                                                <template x-for="r in results" :key="r.id">
                                                    <div @click="select(r)"
                                                        class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700 flex justify-between items-center">
                                                        <span x-text="r.name || r.text" class="font-medium"></span>
                                                        <span x-show="r.unit_cost" class="text-[10px] text-indigo-600 font-mono" x-text="'$' + r.unit_cost"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    
                                    </template>
                                </td>

                                <!-- Target MPR Context Matrix Dropdown -->
                                <td class="p-2.5">
                                    <select x-model="row.matrix_target" @change="calculateRowQty(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <!-- Entire Order -->
                                        <option value="ALL" x-text="'All Matrix (Entire Order Qty: ' + totalMprOrderQty + ' Pcs)'"></option>

                                        <!-- Grouped By Color Totals -->
                                        <template x-if="colorTotals && colorTotals.length > 0">
                                            <optgroup label="--- Color Level Totals ---">
                                                <template x-for="colorGroup in colorTotals" :key="colorGroup.color_name">
                                                    <option :value="`COLOR:${colorGroup.color_name}`" x-text="`${colorGroup.color_name} (Total ${colorGroup.total_quantity} Pcs)`"></option>
                                                </template>
                                            </optgroup>
                                        </template>

                                        <!-- Individual Color/Size Combinations -->
                                        <template x-if="availableMprMatrix && availableMprMatrix.length > 0">
                                            <optgroup label="--- Individual Matrix Items ---">
                                                <template x-for="mprItem in availableMprMatrix" :key="mprItem.id">
                                                    <option :value="`ITEM:${mprItem.id}`" x-text="`${mprItem.color_name} / ${mprItem.size_name} (${mprItem.quantity} Pcs)`"></option>
                                                </template>
                                            </optgroup>
                                        </template>
                                    </select>
                                </td>

                                <!-- Garment Qty -->
                                <td class="p-2.5">
                                    <input type="number" :value="row.garment_qty" readonly class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg text-right font-mono text-slate-600 font-bold" placeholder="0">
                                </td>

                                <!-- Consumption / Unit Rate Multiplier -->
                                <td class="p-2.5">
                                    <input type="number" step="0.0001" x-model.number="row.consumption" @input="calculateRowQty(row)" :placeholder="row.cost_type === 'Processing' ? '1.0000' : '0.0000'" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Required Material / Service Qty -->
                                <td class="p-2.5">
                                    <input type="number" step="0.01" :value="row.req_qty" readonly :class="row.cost_type === 'Processing' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-indigo-50 border-indigo-100 text-indigo-700'" class="w-full p-1.5 text-xs border font-mono font-bold rounded-lg text-right" placeholder="0">
                                </td>

                                <!-- Unit Price / Rate -->
                                <td class="p-2.5">
                                    <input type="number" step="0.0001" x-model.number="row.unit_price" @input="calculateRowQty(row)" placeholder="0.0000" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Total Cost -->
                                <td class="p-2.5 text-right font-mono font-bold text-slate-900" x-text="'$' + (row.total_cost || 0).toFixed(2)"></td>

                                <!-- Action -->
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeBomRow(index)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <!-- Subtotal Summaries -->
                        <tr class="bg-slate-50/50 text-slate-600 text-xs border-t border-slate-200">
                            <td colspan="7" class="p-2 pl-4 text-right font-semibold">Total Raw Materials Cost:</td>
                            <td class="p-2 text-right font-mono font-bold text-indigo-600" x-text="'$' + materialSubtotal.toFixed(2)"></td>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-50/50 text-slate-600 text-xs border-t border-slate-100">
                            <td colspan="7" class="p-2 pl-4 text-right font-semibold">Total Processing & Services Cost (Print/Emb/Wash/CM):</td>
                            <td class="p-2 text-right font-mono font-bold text-emerald-600" x-text="'$' + processingSubtotal.toFixed(2)"></td>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-100 border-t-2 border-slate-200 font-bold text-slate-800 text-xs">
                            <td colspan="7" class="p-3 pl-4 text-right uppercase tracking-wider text-slate-500">Grand Total Cost Budget:</td>
                            <td class="p-3 text-right font-mono text-indigo-700 font-bold text-sm" x-text="'$' + grandTotalBudget.toFixed(2)"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 3. Actions -->
        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('tenant.merch.bom.index') }}" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</a>
            <button type="submit" :disabled="isSaving" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-400 rounded-xl shadow-sm transition">
                <span x-text="isSaving ? 'Saving...' : (isEdit ? 'Update Production BOM' : 'Save Production BOM')"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function productionBomApp(stylesData, initStyleId = null, initMprId = null, initMpr = null, editData = null) {
    return {
        isEdit: !!editData,
        bomId: editData ? editData.id : null,
        availableStyles: stylesData || [],
        
        selectedStyleId: initStyleId || (editData ? editData.style_id : ''),
        selectedMprId: initMprId || (editData ? editData.sales_order_id : ''),
        buyerName: '',
        
        mprOrders: [],
        availableMprMatrix: [],
        colorTotals: [],
        totalMprOrderQty: 0,

        loadingMprs: false,
        isSaving: false,

        items: [],

        init() {
            if (editData && editData.items) {
                this.items = editData.items.map(item => {
                    let target = 'ALL';
                    if (item.sales_order_item_id) {
                        target = `ITEM:${item.sales_order_item_id}`;
                    } else if (item.color_name) {
                        target = `COLOR:${item.color_name}`;
                    }

                    return {
                        cost_type: item.cost_type || (['Print Cost', 'Embroidery Cost', 'Wash Cost', 'Special Process', 'Testing & Inspection', 'CM / Overhead'].includes(item.cost_head) ? 'Processing' : 'Material'),
                        cost_head: item.cost_head || item.category_name || 'Fabric',
                        cat_id: item.cat_id || item.category_id || '',
                        bom_item_id: item.bom_item_id || item.item_id || '',
                        item_name: item.item_name || '',
                        matrix_target: target,
                        sales_order_item_id: item.sales_order_item_id || null,
                        color_name: item.color_name || null,
                        garment_qty: item.garment_qty || 0,
                        consumption: parseFloat(item.consumption) || (item.cost_type === 'Processing' ? 1.0 : 0),
                        req_qty: parseFloat(item.req_qty) || 0,
                        unit_price: parseFloat(item.unit_price) || 0,
                        total_cost: parseFloat(item.total_cost) || 0
                    };
                });
            } else {
                this.addBomRow('Material');
                this.addBomRow('Processing');
            }

            if (this.selectedStyleId) {
                this.onStyleChange(true, initMpr);
            }
        },

        mprTotalQty(mpr) {
            if (!mpr) return 0;
            if (mpr.total_quantity) return mpr.total_quantity;
            if (mpr.items && mpr.items.length) {
                return mpr.items.reduce((sum, i) => sum + (parseInt(i.quantity || i.qty || 0)), 0);
            }
            return 0;
        },

        parseMprItems(mpr) {
            if (!mpr || !mpr.matrix_items) return [];
            return mpr.matrix_items.map(item => ({
                id: item.id,
                color_name: item.color_context ? (item.color_context.color_name || item.color_context.name) : (item.color_name || 'N/A'),
                size_name: item.size_chart ? (item.size_chart.size_name || item.size_chart.name) : (item.size_name || 'N/A'),
                quantity: parseInt(item.quantity || item.qty || 0)
            }));
        },

        onStyleChange(isInitial = false, directMpr = null) {
            if (!this.selectedStyleId) return;

            this.loadingMprs = true;
            fetch(`/merchandising/mpr-order/get-by-style/${this.selectedStyleId}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        this.mprOrders = res.mpr_orders || [];
                        this.buyerName = res.buyer_name || (directMpr && directMpr.buyer ? directMpr.buyer.name : 'N/A');

                        if (directMpr) {
                            const exists = this.mprOrders.some(m => String(m.id) === String(directMpr.id));
                            if (!exists) this.mprOrders.push(directMpr);
                        }

                        if (!isInitial) {
                            this.selectedMprId = '';
                            this.availableMprMatrix = [];
                            this.colorTotals = [];
                            this.totalMprOrderQty = 0;
                            this.items.forEach(row => this.resetRow(row));
                        } else if (this.selectedMprId) {
                            this.onMprChange(directMpr);
                        }
                    }
                    this.loadingMprs = false;
                })
                .catch(() => { this.loadingMprs = false; });
        },

        onMprChange(directMpr = null) {
            let selectedMpr = this.mprOrders.find(m => String(m.id) === String(this.selectedMprId));
            if (!selectedMpr && directMpr && String(directMpr.id) === String(this.selectedMprId)) {
                selectedMpr = directMpr;
            }

            if (selectedMpr) {
                if (selectedMpr.buyer) {
                    this.buyerName = selectedMpr.buyer.name || selectedMpr.buyer.buyer_name || this.buyerName;
                }

                const parsedMatrix = this.parseMprItems(selectedMpr);
                const totalQty = this.mprTotalQty(selectedMpr);

                const colorMap = {};
                parsedMatrix.forEach(m => {
                    const cName = m.color_name || 'Unassigned';
                    if (!colorMap[cName]) {
                        colorMap[cName] = 0;
                    }
                    colorMap[cName] += m.quantity;
                });

                const parsedColorTotals = Object.keys(colorMap).map(cName => ({
                    color_name: cName,
                    total_quantity: colorMap[cName]
                }));

                this.availableMprMatrix = [...parsedMatrix];
                this.totalMprOrderQty = totalQty;
                this.colorTotals = [...parsedColorTotals];

                this.items.forEach(row => this.calculateRowQty(row));
            } else {
                this.availableMprMatrix = [];
                this.colorTotals = [];
                this.totalMprOrderQty = 0;
            }
        },

        calculateRowQty(row) {
            const target = row.matrix_target || 'ALL';

            if (target.startsWith('ITEM:')) {
                const itemId = target.replace('ITEM:', '');
                const matrix = this.availableMprMatrix.find(m => String(m.id) === String(itemId));
                row.sales_order_item_id = itemId;
                row.color_name = matrix ? matrix.color_name : null;
                row.garment_qty = matrix ? matrix.quantity : 0;
            } else if (target.startsWith('COLOR:')) {
                const cName = target.replace('COLOR:', '');
                const colGroup = this.colorTotals.find(c => c.color_name === cName);
                row.sales_order_item_id = null;
                row.color_name = cName;
                row.garment_qty = colGroup ? colGroup.total_quantity : 0;
            } else {
                row.sales_order_item_id = null;
                row.color_name = null;
                row.garment_qty = this.totalMprOrderQty;
            }

            row.req_qty = (row.garment_qty * (row.consumption || 0)).toFixed(2);
            row.total_cost = (parseFloat(row.req_qty) * (row.unit_price || 0));
        },

        addBomRow(type = 'Material') {
            const newRow = {
                cost_type: type,
                cost_head: type === 'Processing' ? 'Print Cost' : 'Fabric',
                cat_id: '',
                cat_name: '',
                bom_item_id: '',
                item_name: '',
                matrix_target: 'ALL',
                sales_order_item_id: null,
                color_name: null,
                garment_qty: 0,
                consumption: type === 'Processing' ? 1 : 0,
                req_qty: 0,
                unit_price: 0,
                total_cost: 0
            };
            this.items.push(newRow);
            if (this.selectedMprId) this.calculateRowQty(newRow);
        },

        removeBomRow(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        resetRow(row) {
            row.cat_id = '';
            row.cat_name = '';
            row.bom_item_id = '';
            row.item_name = '';
            row.matrix_target = 'ALL';
            row.sales_order_item_id = null;
            row.color_name = null;
            row.garment_qty = 0;
            row.consumption = row.cost_type === 'Processing' ? 1 : 0;
            row.req_qty = 0;
            row.unit_price = 0;
            row.total_cost = 0;
        },

        get materialSubtotal() {
            return this.items
                .filter(row => row.cost_type !== 'Processing')
                .reduce((sum, row) => sum + (parseFloat(row.total_cost) || 0), 0);
        },

        get processingSubtotal() {
            return this.items
                .filter(row => row.cost_type === 'Processing')
                .reduce((sum, row) => sum + (parseFloat(row.total_cost) || 0), 0);
        },

        get grandTotalBudget() {
            return this.materialSubtotal + this.processingSubtotal;
        },

        submitBom() {
            if (!this.selectedStyleId || !this.selectedMprId) {
                Swal.fire('Warning', 'Please select both Style and MPR Order context.', 'warning');
                return;
            }

            this.isSaving = true;

            let targetUrl = this.isEdit 
                ? `/merchandising/bom/${this.bomId}` 
                : "{{ route('tenant.merch.bom.store') }}";
                
            let httpMethod = this.isEdit ? "PUT" : "POST";

            fetch(targetUrl, {
                method: httpMethod,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    style_id: this.selectedStyleId,
                    sales_order_id: this.selectedMprId,
                    grand_total: this.grandTotalBudget,
                    items: this.items
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSaving = false;
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: "Production BOM & Cost Sheet saved successfully!",
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('tenant.merch.bom.index') }}";
                    });
                } else {
                    Swal.fire('Error', data.message || 'Validation failed.', 'error');
                }
            })
            .catch(() => {
                this.isSaving = false;
                Swal.fire('Error', 'Network error or server exception occurred.', 'error');
            });
        }
    }
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

function itemSearchBox(row, mainApp) {
    return {
        query: row.item_name || '',
        results: [],
        open: false,
        loading: false,
        _timer: null,
        _controller: null,
        _seq: 0,

        search() {
            row.bom_item_id = '';
            row.item_name = this.query;

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
                cost_head: row.cost_head || ''
            });

            try {
                const res = await fetch(
                    "{{ route('tenant.api.item_masters.search') }}?" + params.toString(),
                    { signal: this._controller.signal, headers: { 'Accept': 'application/json' } }
                );
                const data = await res.json();

                if (seq !== this._seq) return;

                this.results = data.results || [];
                this.open = true;
            } catch (e) {
                if (e.name !== 'AbortError') console.error(e);
            } finally {
                if (seq === this._seq) this.loading = false;
            }
        },

        select(r) {
            row.bom_item_id = r.id;
            row.item_name = r.name || r.text;
            
            if (r.unit_cost) row.unit_price = parseFloat(r.unit_cost);

            this.query = r.name || r.text;
            this.results = [];
            this.open = false;

            if (mainApp && typeof mainApp.calculateRowQty === 'function') {
                mainApp.calculateRowQty(row);
            }
        }
    };
}
</script>
@endpush