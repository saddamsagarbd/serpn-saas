@extends('layouts.tenant')
@section('title', isset($bom) ? 'Edit Production BOM' : 'Create Production BOM')

@push('styles')
<style>
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
                            <option :value="String(style.id)" :selected="String(style.id) === String(selectedStyleId)" x-text="(style.style_code || style.style_number) + ' - ' + (style.style_name || '')"></option>
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
                            <option :value="String(mpr.id)" :selected="String(mpr.id) === String(selectedMprId)" x-text="(mpr.buyer_po_number || mpr.order_number || ('MPR #' + mpr.id)) + ' (' + mprTotalQty(mpr) + ' Pcs)'"></option>
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

            <div class="border border-slate-200/80 rounded-xl overflow-x-auto shadow-sm bg-white">
                <table class="w-full text-left border-collapse min-w-[1050px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-2/12" title="Category / Cost Head">Category</th>
                            <th class="p-3 w-2/12" title="Item / Service Description">Item / Service</th>
                            <th class="p-3 w-1/12" title="Item Color">Color</th>
                            <th class="p-3 w-2/12" title="MPR Garment Matrix">MPR Matrix</th>
                            <th class="p-3 w-1/12 text-right">GMT Qty</th>
                            <th class="p-3 w-1/12 text-right" title="Costing Consumption">Cons.</th>
                            <th class="p-3 w-1/12 text-right" title="Wastage (%)">Wst. %</th>
                            <th class="p-3 w-1/12 text-right" title="Total Required Qty">Req. Qty</th>
                            <th class="p-3 w-1/12 text-right" title="Unit Price ($)">Price ($)</th>
                            <th class="p-3 w-1/12 text-right" title="Total Cost ($)">Total ($)</th>
                            <th class="p-3 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        
                        <!-- A. RAW MATERIALS & TRIMS GROUP -->
                        <tr class="bg-slate-100/80 border-y border-slate-200 font-bold text-slate-700 text-[11px]">
                            <td colspan="11" class="px-4 py-2 uppercase tracking-wider">A. Raw Materials & Trims Breakdown</td>
                        </tr>

                        <template x-for="(row, index) in items" :key="index">
                            <tr x-show="row.cost_type !== 'Processing'" class="hover:bg-slate-50/50 transition">
                                <!-- Category Search / Input -->
                                <td class="p-2 pl-4" x-data="categorySearchBox(row)" @click.outside="open = false">
                                    <div class="relative">
                                        <input type="text"
                                            x-model="query"
                                            x-effect="query = row.cat_name || ''"
                                            @input="search()"
                                            @focus="if (results.length) open = true"
                                            placeholder="Search Category..."
                                            class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">

                                        <div x-show="open" x-cloak class="absolute left-0 top-full mt-1 w-64 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl text-xs z-50">
                                            <template x-for="r in results" :key="r.id">
                                                <div @click="select(r)" class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700" x-text="r.name || r.text"></div>
                                            </template>
                                        </div>
                                    </div>
                                </td>

                                <!-- Material Item Search / Input -->
                                <td class="p-2" x-data="itemSearchBox(row, $data)" @click.outside="open = false">
                                    <div class="relative">
                                        <input type="text"
                                            x-model="query"
                                            x-effect="query = row.item_name || ''"
                                            @input="search()"
                                            @focus="if (results.length) open = true"
                                            placeholder="Search Material..."
                                            class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">

                                        <div x-show="open" x-cloak class="absolute left-0 top-full mt-1 w-64 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-xl text-xs z-50">
                                            <template x-for="r in results" :key="r.id">
                                                <div @click="select(r)" class="p-2 hover:bg-indigo-50 cursor-pointer text-slate-700 flex justify-between items-center">
                                                    <span x-text="r.name || r.text" class="font-medium"></span>
                                                    <span class="text-indigo-600 font-mono text-[10px]" x-text="'$' + (r.unit_cost || 0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </td>

                                <!-- Item Color Column -->
                                <td class="p-2">
                                    <input type="text" x-model="row.item_color" placeholder="e.g. As Match" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                </td>

                                <!-- MPR Matrix Selection -->
                                <td class="p-2">
                                    <select @change="row.matrix_target = $event.target.value; calculateRowQty(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <template x-for="opt in matrixOptionsFor(row)" :key="opt.value">
                                            <option :value="opt.value" :selected="opt.value === row.matrix_target" x-text="opt.label"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- Garment Qty -->
                                <td class="p-2">
                                    <input type="number" :value="row.garment_qty" readonly class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg text-right font-mono text-slate-600 font-bold">
                                </td>

                                <!-- Costing Consumption -->
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="row.consumption" @input="calculateRowQty(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Booking Wastage % -->
                                <td class="p-2">
                                    <input type="number" step="0.1" x-model.number="row.wastage_percent" @input="calculateRowQty(row)" placeholder="0%" class="w-full p-1.5 text-xs bg-amber-50/50 border border-amber-200 rounded-lg text-right font-mono focus:outline-none focus:border-amber-500 text-amber-900 font-semibold">
                                </td>

                                <!-- Total Req Qty -->
                                <td class="p-2">
                                    <input type="number" step="0.01" :value="row.req_qty" readonly class="w-full p-1.5 text-xs bg-indigo-50 border border-indigo-100 text-indigo-700 font-mono font-bold rounded-lg text-right">
                                </td>

                                <!-- Unit Price -->
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="row.unit_price" @input="calculateRowQty(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Total Cost -->
                                <td class="p-2 text-right font-mono font-bold text-slate-900" x-text="'$' + (parseFloat(row.total_cost) || 0).toFixed(2)"></td>

                                <!-- Remove Row -->
                                <td class="p-2 text-center">
                                    <button type="button" @click="removeBomRow(index)" class="text-slate-400 hover:text-rose-600 p-1">✕</button>
                                </td>
                            </tr>
                        </template>

                        <!-- B. VALUE ADDITION COSTS AT BOTTOM -->
                        <tr class="bg-emerald-100/80 border-y border-emerald-200 font-bold text-emerald-800 text-[11px]">
                            <td colspan="11" class="px-4 py-2 uppercase tracking-wider">B. Value Addition Costs (Printing, Embroidery, Washing & Overhead)</td>
                        </tr>

                        <template x-for="(row, index) in items" :key="'proc_' + index">
                            <tr x-show="row.cost_type === 'Processing'" class="bg-emerald-50/20 hover:bg-emerald-50/40 transition">
                                <!-- Processing Service Category -->
                                <td class="p-2 pl-4">
                                    <select x-model="row.cost_head" @change="row.cat_name = row.cost_head" class="w-full p-1.5 text-xs bg-emerald-50/60 border border-emerald-200 rounded-lg focus:outline-none focus:border-emerald-500 font-semibold text-emerald-800">
                                        <option value="Print Cost">Printing Service</option>
                                        <option value="Embroidery Cost">Embroidery Service</option>
                                        <option value="Wash Cost">Washing Service</option>
                                        <option value="Special Process">Special Treatment / Dyeing</option>
                                        <option value="Testing & Inspection">Lab Testing / Inspection</option>
                                        <option value="CM Cost">CM Cost</option>
                                        <option value="Overhead Cost">Overhead Cost</option>
                                    </select>
                                </td>

                                <!-- Process Item Selection -->
                                <td class="p-2" colspan="2">
                                    <select x-model="row.item_id" @change="onProcessSelect(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 font-medium text-slate-800">
                                        <option value="">-- Select Process / Work --</option>
                                        <template x-for="proc in getStyleProcesses(row.cost_head)" :key="proc.id">
                                            <option :value="proc.id" :selected="row.item_id == proc.id" x-text="proc.name + (proc.rate ? ' ($' + proc.rate + ')' : '')"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- Matrix Target Context (fixed to MPR total) -->
                                <td class="p-2">
                                    <div class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg text-slate-600 font-semibold" x-text="'MPR Total (' + totalMprOrderQty + ' Pcs)'"></div>
                                </td>

                                <!-- GMT Qty -->
                                <td class="p-2">
                                    <input type="number" :value="row.garment_qty" readonly class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg text-right font-mono text-slate-600 font-bold">
                                </td>

                                <!-- Consumption N/A (qty = MPR order qty) -->
                                <td class="p-2 text-center text-slate-300 font-mono text-[10px] align-middle">-</td>

                                <!-- Wastage N/A -->
                                <td class="p-2 text-center text-slate-300 font-mono text-[10px] align-middle">-</td>

                                <!-- Total Service Qty -->
                                <td class="p-2">
                                    <input type="number" step="0.01" :value="row.req_qty" readonly class="w-full p-1.5 text-xs bg-emerald-50 border border-emerald-100 text-emerald-700 font-mono font-bold rounded-lg text-right">
                                </td>

                                <!-- Service Price -->
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="row.unit_price" @input="calculateRowQty(row)" placeholder="0.0000" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-emerald-500" required>
                                </td>

                                <!-- Service Total Cost -->
                                <td class="p-2 text-right font-mono font-bold text-slate-900" x-text="'$' + (parseFloat(row.total_cost) || 0).toFixed(2)"></td>

                                <!-- Action -->
                                <td class="p-2 text-center">
                                    <button type="button" @click="removeBomRow(index)" class="text-slate-400 hover:text-rose-600 p-1">✕</button>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 text-slate-600 text-xs border-t border-slate-200">
                            <td colspan="9" class="p-2 pl-4 text-right font-semibold">Total Raw Materials Cost:</td>
                            <td class="p-2 text-right font-mono font-bold text-indigo-600" x-text="'$' + materialSubtotal.toFixed(2)"></td>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-50 text-slate-600 text-xs border-t border-slate-100">
                            <td colspan="9" class="p-2 pl-4 text-right font-semibold">Total Value Addition Cost (Print/Emb/Wash/CM):</td>
                            <td class="p-2 text-right font-mono font-bold text-emerald-600" x-text="'$' + processingSubtotal.toFixed(2)"></td>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-100 border-t-2 border-slate-200 font-bold text-slate-800 text-xs">
                            <td colspan="9" class="p-3 pl-4 text-right uppercase tracking-wider text-slate-500">Grand Total Budget:</td>
                            <td class="p-3 text-right font-mono text-indigo-700 font-bold text-sm" x-text="'$' + grandTotalBudget.toFixed(2)"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions -->
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
        
        selectedStyleId: initStyleId ? String(initStyleId) : (editData && editData.style_id ? String(editData.style_id) : ''),
        selectedMprId: initMprId ? String(initMprId) : (editData && editData.sales_order_id ? String(editData.sales_order_id) : ''),
        buyerName: '',
        
        mprOrders: [],
        availableMprMatrix: [],
        colorTotals: [],
        totalMprOrderQty: 0,
        
        bom_items: [],
        styleCosting: { processes: [] },

        loadingMprs: false,
        isSaving: false,
        items: [],

        init() {
            if (editData && editData.items && editData.items.length > 0) {
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
                        cat_name: item.cat_name || item.category_name || '',
                        item_id: item.item_id || '',
                        item_name: item.item_name || '',
                        item_color: item.item_color || '',
                        matrix_target: target,
                        sales_order_item_id: item.sales_order_item_id || null,
                        color_name: item.color_name || null,
                        garment_qty: item.garment_qty || 0,
                        consumption: parseFloat(item.consumption) || (item.cost_type === 'Processing' ? 1.0 : 0),
                        wastage_percent: parseFloat(item.wastage_percent) || 0,
                        req_qty: parseFloat(item.req_qty) || 0,
                        unit_price: parseFloat(item.unit_price) || parseFloat(item.unit_cost) || 0,
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

        onStyleChange(isInitial = false, directMpr = null) {
            if (!this.selectedStyleId) return;

            this.loadingMprs = true;
            fetch(`/merchandising/mpr-order/get-by-style/${this.selectedStyleId}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        this.mprOrders = res.mpr_orders || [];
                        this.buyerName = res.buyer_name || (directMpr && directMpr.buyer ? directMpr.buyer.name : 'N/A');
                        
                        if (!this.isEdit) {
                            this.bom_items = res.bom_items || [];
                        }

                        if (res.style_costing) {
                            this.styleCosting = res.style_costing;
                        }

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
                            this.selectedMprId = String(this.selectedMprId);
                            this.onMprChange(isInitial);
                        }
                    }
                    this.loadingMprs = false;
                })
                .catch(err => {
                    console.error("Error fetching style details:", err);
                    this.loadingMprs = false;
                });
        },

        onMprChange(isInitial = false) {
            let selectedMpr = this.mprOrders.find(m => String(m.id) === String(this.selectedMprId));

            if (!selectedMpr) {
                this.colorTotals = [];
                this.availableMprMatrix = [];
                this.totalMprOrderQty = 0;
                return;
            }

            this.totalMprOrderQty = selectedMpr.total_quantity || this.mprTotalQty(selectedMpr);
            this.availableMprMatrix = selectedMpr.matrix_items || selectedMpr.items || [];

            const colorMap = {};
            if (this.availableMprMatrix.length) {
                this.availableMprMatrix.forEach(m => {
                    const cName = m.color_name || (m.color_context ? m.color_context.name : 'Unassigned');
                    const cid = m.id || '';
                    colorMap[cName] = (colorMap[cName] || 0) + (parseInt(m.quantity || m.qty || 0));
                });
            }

            this.colorTotals = Object.keys(colorMap).map(cName => ({
                color_name: cName,
                total_quantity: colorMap[cName]
            }));

            // Do not reset loaded BOM rows during Edit initialization
            if (isInitial && this.isEdit) {
                this.items.forEach(row => this.calculateRowQty(row));
                return;
            }

            // Auto Populate for New Creation Mode
            let autoPopulatedItems = [];
            if (this.bom_items && this.bom_items.length > 0) {
                this.bom_items.forEach(b => {
                    if (b.cost_type === 'Material' && this.colorTotals.length > 0) {
                        this.colorTotals.forEach(col => {
                            const baseQty = col.total_quantity * b.consumption;
                            const wastageQty = baseQty * ((b.wastage_percent || 0) / 100);
                            const reqQty = (baseQty + wastageQty).toFixed(2);
                            const totalCost = (parseFloat(reqQty) * b.unit_cost).toFixed(2);
                            
                            autoPopulatedItems.push({
                                cost_type: 'Material',
                                cost_head: b.cat_name || 'Material',
                                cat_id: b.cat_id,
                                cat_name: b.cat_name,
                                item_id: b.item_id,
                                item_name: b.item_name,
                                item_color: b.item_color || '',
                                matrix_target: `COLOR:${col.color_name}`,
                                sales_order_item_id: null,
                                color_name: col.color_name,
                                garment_qty: col.total_quantity,
                                consumption: b.consumption,
                                wastage_percent: b.wastage_percent || 0,
                                req_qty: reqQty,
                                unit_price: b.unit_cost,
                                total_cost: parseFloat(totalCost)
                            });
                        });
                    } else if (b.cost_type === 'Processing') {
                        const totalGmt = this.totalMprOrderQty || 0;
                        const cons = 1.0;
                        const reqQty = parseFloat(totalGmt).toFixed(2);
                        const unitPrice = parseFloat(b.unit_cost) || 0;
                        const totalCost = (parseFloat(reqQty) * unitPrice).toFixed(2);

                        autoPopulatedItems.push({
                            cost_type: 'Processing',
                            cost_head: b.cost_head || 'Print Cost',
                            cat_id: '',
                            cat_name: b.cost_head || 'Print Cost',
                            item_id: b.id || null,
                            item_name: b.item_name || '',
                            item_color: '',
                            matrix_target: 'ALL',
                            sales_order_item_id: null,
                            color_name: 'ALL COLORS',
                            garment_qty: totalGmt, // Ensure garment_qty is set to MPR total Qty
                            consumption: cons,
                            wastage_percent: 0,
                            req_qty: reqQty,
                            unit_price: unitPrice,
                            total_cost: parseFloat(totalCost)
                        });
                    }
                });

                if (autoPopulatedItems.length > 0) {
                    this.items = autoPopulatedItems;
                }
            }

            // Any material row still on "All Matrix" becomes one row per MPR color
            if (this.colorTotals.length > 0) {
                [...this.items].filter(r => r.cost_type !== 'Processing' && (r.matrix_target || 'ALL') === 'ALL')
                    .forEach(r => this.splitRowByColor(r));
            }

            // Keep every row (incl. value addition rows) in sync with the selected MPR qty
            this.items.forEach(row => this.calculateRowQty(row));
        },

        calculateRowQty(row) {
            // Value addition (Processing): Total Req. Qty = MPR order qty
            if (row.cost_type === 'Processing') {
                const mprQty = parseInt(this.totalMprOrderQty) || 0;
                row.matrix_target = 'ALL';
                row.sales_order_item_id = null;
                row.color_name = null;
                row.garment_qty = mprQty;
                row.consumption = 1;
                row.wastage_percent = 0;
                row.req_qty = mprQty.toFixed(2);
                row.total_cost = mprQty * (parseFloat(row.unit_price) || 0);
                return;
            }

            const target = row.matrix_target || 'ALL';

            if (target.startsWith('ITEM:')) {
                const itemId = target.replace('ITEM:', '');
                const matrix = this.availableMprMatrix.find(m => String(m.id) === String(itemId));
                row.sales_order_item_id = itemId;
                row.color_name = matrix ? matrix.color_name : null;
                row.garment_qty = matrix ? (parseInt(matrix.quantity || matrix.qty || 0)) : 0;
            } else if (target.startsWith('COLOR:')) {
                const cName = target.replace('COLOR:', '');
                const colGroup = this.colorTotals.find(c => c.color_name === cName);
                row.sales_order_item_id = null;
                row.color_name = cName;
                row.garment_qty = colGroup ? colGroup.total_quantity : 0;
            } else { // 'ALL' Matrix Selected
                row.sales_order_item_id = null;
                row.color_name = null;
                row.garment_qty = this.totalMprOrderQty || 0;
            }

            const baseQty = (row.garment_qty || 0) * (row.consumption || 0);
            const wastageQty = row.cost_type !== 'Processing' ? (baseQty * ((row.wastage_percent || 0) / 100)) : 0;

            row.req_qty = (baseQty + wastageQty).toFixed(2);
            row.total_cost = (parseFloat(row.req_qty) * (row.unit_price || 0));
        },

        getStyleProcesses(costHead) {
            if (!this.styleCosting || !this.styleCosting.processes) {
                return [
                    { id: 'p1', name: 'Chest Rubber Print', rate: 0.35, cost_head: 'Print Cost' },
                    { id: 'p2', name: 'All Over Print (AOP)', rate: 0.65, cost_head: 'Print Cost' },
                    { id: 'e1', name: 'Logo Embroidery Cost', rate: 0.25, cost_head: 'Embroidery Cost' },
                    { id: 'w1', name: 'Garment Enzyme Wash', rate: 0.40, cost_head: 'Wash Cost' },
                    { id: 'c1', name: 'Factory CM Rate', rate: 1.20, cost_head: 'CM / Overhead' }
                ].filter(p => !costHead || p.cost_head === costHead);
            }

            return this.styleCosting.processes.filter(p => p.cost_head === costHead);
        },

        onProcessSelect(row) {
            const processes = this.getStyleProcesses(row.cost_head);
            const selectedProc = processes.find(p => String(p.id) === String(row.item_id));

            if (selectedProc) {
                row.item_name = selectedProc.name;
                row.unit_price = parseFloat(selectedProc.rate || selectedProc.unit_cost || 0);
                if (!row.consumption || row.consumption == 0) {
                    row.consumption = 1;
                }
            } else {
                row.unit_price = 0;
            }

            this.calculateRowQty(row);
        },

        addBomRow(type = 'Material') {
            const isProcessing = type === 'Processing';

            // Material line => one row per MPR color, each with that color's qty
            if (!isProcessing && this.colorTotals && this.colorTotals.length > 0) {
                const groupKey = 'g' + Date.now() + Math.random().toString(36).slice(2, 6);
                this.colorTotals.forEach(col => {
                    const r = {
                        cost_type: 'Material', cost_head: 'Fabric',
                        cat_id: '', cat_name: '', item_id: '', item_name: '', item_color: '',
                        matrix_target: `COLOR:${col.color_name}`, group_key: groupKey,
                        sales_order_item_id: null, color_name: col.color_name,
                        garment_qty: col.total_quantity, consumption: 0, wastage_percent: 0,
                        req_qty: '0.00', unit_price: 0, total_cost: 0
                    };
                    this.items.push(r);
                    this.calculateRowQty(r);
                });
                return;
            }

            const initialGmtQty = isProcessing ? (this.totalMprOrderQty || 0) : 0;
            const initialCons = isProcessing ? 1 : 0;

            const newRow = {
                cost_type: type,
                cost_head: isProcessing ? 'Print Cost' : 'Fabric',
                cat_id: '',
                cat_name: '',
                item_id: '',
                item_name: '',
                item_color: '',
                matrix_target: 'ALL',
                sales_order_item_id: null,
                color_name: null,
                garment_qty: initialGmtQty,
                consumption: initialCons,
                wastage_percent: 0,
                req_qty: (initialGmtQty * initialCons).toFixed(2),
                unit_price: 0,
                total_cost: 0
            };

            this.items.push(newRow);
            if (this.selectedMprId) {
                this.calculateRowQty(newRow);
            }
        },

        // Flat option list for the matrix dropdown (All only when no colors / legacy rows)
        matrixOptionsFor(row) {
            const opts = [];
            if (!this.colorTotals.length || (row.matrix_target || 'ALL') === 'ALL') {
                opts.push({ value: 'ALL', label: 'All Matrix (' + this.totalMprOrderQty + ' Pcs)' });
            }
            this.colorTotals.forEach(c => opts.push({
                value: 'COLOR:' + c.color_name,
                label: c.color_name + ' (' + c.total_quantity + ' Pcs)'
            }));
            return opts;
        },

        // One row per MPR color for a material item (e.g. Shell Fabric - Beige 300, Shell Fabric - Olive 300)
        splitRowByColor(row) {
            if (row.cost_type === 'Processing' || !this.colorTotals || this.colorTotals.length < 2) return;
            if ((row.matrix_target || 'ALL') !== 'ALL') return;

            const idx = this.items.indexOf(row);
            if (idx === -1) return;
            const groupKey = row.group_key || ('g' + Date.now() + Math.random().toString(36).slice(2, 6));
            row.group_key = groupKey;

            const extraRows = this.colorTotals.slice(1).map(col => {
                const copy = Object.assign({}, row, {
                    matrix_target: `COLOR:${col.color_name}`,
                    sales_order_item_id: null,
                    color_name: col.color_name
                });
                this.calculateRowQty(copy);
                return copy;
            });

            row.matrix_target = `COLOR:${this.colorTotals[0].color_name}`;
            this.calculateRowQty(row);
            this.items.splice(idx + 1, 0, ...extraRows);
        },

        removeBomRow(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        resetRow(row) {
            row.cat_id = '';
            row.cat_name = '';
            row.item_id = '';
            row.item_name = '';
            row.item_color = '';
            row.matrix_target = 'ALL';
            row.sales_order_item_id = null;
            row.color_name = null;
            row.garment_qty = 0;
            row.consumption = row.cost_type === 'Processing' ? 1 : 0;
            row.wastage_percent = 0;
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
                        text: "Production BOM saved successfully!",
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
            this.loading = true;
            try {
                const res = await fetch("{{ route('tenant.api.category_masters.search') }}?q=" + encodeURIComponent(this.query), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.results = data.results || [];
                this.open = true;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
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

        search() {
            row.item_id = '';
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
            this.loading = true;
            const params = new URLSearchParams({
                q: this.query,
                cost_head: row.cost_head || '',
                style_id: mainApp.selectedStyleId || ''
            });

            try {
                const res = await fetch("{{ route('tenant.api.item_masters.search') }}?" + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.results = data.results || [];
                this.open = true;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        select(r) {
            row.item_id = r.id;
            row.item_name = r.name || r.text;
            if (r.unit_cost || r.unit_price) {
                row.unit_price = parseFloat(r.unit_cost || r.unit_price);
            }
            if (r.consumption) {
                row.consumption = parseFloat(r.consumption);
            }
            this.query = r.name || r.text;
            this.results = [];
            this.open = false;

            if (mainApp && typeof mainApp.calculateRowQty === 'function') {
                mainApp.calculateRowQty(row);
            }

            // Same item goes to the other color rows of this group (qty stays per color)
            if (mainApp && row.group_key) {
                mainApp.items
                    .filter(r => r !== row && r.group_key === row.group_key && !r.item_id)
                    .forEach(r => {
                        r.cat_id = row.cat_id; r.cat_name = row.cat_name;
                        r.item_id = row.item_id; r.item_name = row.item_name;
                        r.unit_price = row.unit_price; r.consumption = row.consumption;
                        mainApp.calculateRowQty(r);
                    });
            }

            // Auto-create one row per MPR color (qty = that color's MPR qty)
            if (mainApp && typeof mainApp.splitRowByColor === 'function') {
                mainApp.splitRowByColor(row);
            }
        }
    };
}
</script>
@endpush