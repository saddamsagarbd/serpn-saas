@extends('layouts.tenant')
@section('title', isset($bom) ? 'Edit Production BOM' : 'Create Production BOM')

@section('content')
<div x-data="productionBomApp({{ json_encode($styles) }}, {{ json_encode($bom ?? null) }})" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Edit Production BOM' : 'New Production BOM Entry'"></h4>
            <p class="text-xs text-slate-400 mt-0.5">Calculate required raw material quantities based on MPR order quantity and consumption.</p>
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
                            <option :value="style.id" x-text="(style.style_number || style.style_code) + ' - ' + (style.style_name || '')"></option>
                        </template>
                    </select>
                </div>

                <!-- Select MPR / Sales Order -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Select MPR / Sales Order *</label>
                    <select x-model="selectedMprId" @change="onMprChange()" :disabled="!selectedStyleId || loadingMprs" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100" required>
                        <option value="">-- Choose MPR Order --</option>
                        <template x-for="mpr in mprOrders" :key="mpr.id">
                            <option :value="mpr.id" x-text="`${mpr.buyer_po_number} (Total Qty: ${mpr.total_quantity} Pcs)`"></option>
                        </template>
                    </select>
                </div>

                <!-- Buyer Name Display -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Buyer Context</label>
                    <input type="text" :value="buyerName" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-medium cursor-not-allowed" placeholder="Auto-populated">
                </div>
            </div>
        </div>

        <!-- 2. Production BOM Items Breakdown Table -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">2. BOM Material Requirement Planning</h5>
                <button type="button" @click="addBomRow()" :disabled="!selectedMprId" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition disabled:opacity-50">
                    + Add Material Line
                </button>
            </div>

            <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-3/12">Costing Item (BOM Item)</th>
                            <th class="p-3 w-3/12">MPR Color / Size Matrix</th>
                            <th class="p-3 w-1/12 text-right">Garment Qty</th>
                            <th class="p-3 w-1/12 text-right">Cons / GMT</th>
                            <th class="p-3 w-1/12 text-right">Required Material Qty</th>
                            <th class="p-3 w-1/12 text-right">Unit Price ($)</th>
                            <th class="p-3 w-1/12 text-right">Total Cost ($)</th>
                            <th class="p-3 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <template x-for="(row, index) in items" :key="index">
                            <tr>
                                <!-- Costing BOM Item Select -->
                                <td class="p-2.5 pl-4">
                                    <select x-model="row.bom_item_id" @change="onBomItemSelect(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500" required>
                                        <option value="">-- Select Material Item --</option>
                                        <template x-for="item in availableBomItems" :key="item.id">
                                            <option :value="item.id" x-text="`${item.item_name} ($${item.unit_cost})`"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- Target MPR Color/Size Breakdown Item -->
                                <td class="p-2.5">
                                    <select x-model="row.sales_order_item_id" @change="calculateRowQty(row)" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">All Matrix (Entire MPR Context)</option>
                                        <template x-for="mprItem in availableMprMatrix" :key="mprItem.id">
                                            <option :value="mprItem.id" x-text="`${mprItem.color_name} / ${mprItem.size_name} (${mprItem.quantity} Pcs)`"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- Garment Qty (Auto calculated from MPR Selection) -->
                                <td class="p-2.5">
                                    <input type="number" :value="row.garment_qty" readonly class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg text-right font-mono text-slate-600 font-bold" placeholder="0">
                                </td>

                                <!-- Consumption per Garment -->
                                <td class="p-2.5">
                                    <input type="number" step="0.0001" x-model.number="row.consumption" @input="calculateRowQty(row)" placeholder="0.0000" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Required Material Qty = Garment Qty * Consumption -->
                                <td class="p-2.5">
                                    <input type="number" step="0.01" :value="row.req_qty" readonly class="w-full p-1.5 text-xs bg-indigo-50 border border-indigo-100 text-indigo-700 font-mono font-bold rounded-lg text-right" placeholder="0">
                                </td>

                                <!-- Unit Price -->
                                <td class="p-2.5">
                                    <input type="number" step="0.01" x-model.number="row.unit_price" @input="calculateRowQty(row)" placeholder="0.00" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono focus:outline-none focus:border-indigo-500" required>
                                </td>

                                <!-- Total Cost = Required Qty * Unit Price -->
                                <td class="p-2.5 text-right font-mono font-bold text-slate-900" x-text="'$' + row.total_cost.toFixed(2)"></td>

                                <!-- Action -->
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeBomRow(index)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-700 text-xs">
                            <td colspan="6" class="p-3 pl-4 text-right text-[10px] uppercase tracking-wider text-slate-400">Total Material Budget</td>
                            <td class="p-3 text-right font-mono text-indigo-600 font-bold text-sm" x-text="'$' + grandTotalBudget.toFixed(2)"></td>
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
function productionBomApp(stylesData, editData = null) {
    return {
        isEdit: !!editData,
        bomId: editData ? editData.id : null,
        availableStyles: stylesData || [],
        
        selectedStyleId: editData ? (editData.style_id || '') : '',
        selectedMprId: editData ? (editData.sales_order_id || '') : '',
        buyerName: '',
        
        mprOrders: [],
        availableBomItems: [],
        availableMprMatrix: [],
        
        loadingMprs: false,
        isSaving: false,

        items: [],

        init() {
            if (editData && editData.items) {
                this.items = editData.items.map(item => ({
                    bom_item_id: item.bom_item_id || '',
                    sales_order_item_id: item.sales_order_item_id || '',
                    garment_qty: item.garment_qty || 0,
                    consumption: parseFloat(item.consumption) || 0,
                    req_qty: parseFloat(item.req_qty) || 0,
                    unit_price: parseFloat(item.unit_price) || 0,
                    total_cost: parseFloat(item.total_cost) || 0
                }));
            } else {
                this.addBomRow();
            }

            if (this.selectedStyleId) {
                this.onStyleChange(true);
            }
        },

        onStyleChange(isInitial = false) {
            if (!this.selectedStyleId) return;

            this.loadingMprs = true;
            fetch(`/merchandising/mpr-order/get-by-style/${this.selectedStyleId}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        this.buyerName = res.buyer_name || 'N/A';
                        this.availableBomItems = res.bom_items || [];
                        this.mprOrders = res.mpr_orders || [];

                        if (!isInitial) {
                            this.selectedMprId = '';
                            this.availableMprMatrix = [];
                            this.items.forEach(row => this.resetRow(row));
                        } else if (this.selectedMprId) {
                            this.onMprChange();
                        }
                    }
                    this.loadingMprs = false;
                })
                .catch(() => { this.loadingMprs = false; });
        },

        onMprChange() {
            const selectedMpr = this.mprOrders.find(m => String(m.id) === String(this.selectedMprId));
            if (selectedMpr) {
                this.availableMprMatrix = selectedMpr.matrix_items || [];
                // Recalculate garment Qty for existing rows
                this.items.forEach(row => this.calculateRowQty(row));
            } else {
                this.availableMprMatrix = [];
            }
        },

        onBomItemSelect(row) {
            const selectedBom = this.availableBomItems.find(b => String(b.id) === String(row.bom_item_id));
            if (selectedBom) {
                row.unit_price = selectedBom.unit_cost;
                if (!row.consumption) row.consumption = selectedBom.consumption;
                this.calculateRowQty(row);
            }
        },

        calculateRowQty(row) {
            const selectedMpr = this.mprOrders.find(m => String(m.id) === String(this.selectedMprId));
            
            if (row.sales_order_item_id) {
                // Specific Color/Size Matrix Selected
                const matrix = this.availableMprMatrix.find(m => String(m.id) === String(row.sales_order_item_id));
                row.garment_qty = matrix ? matrix.quantity : 0;
            } else {
                // Whole MPR Order Selected
                row.garment_qty = selectedMpr ? selectedMpr.total_quantity : 0;
            }

            row.req_qty = (row.garment_qty * (row.consumption || 0)).toFixed(2);
            row.total_cost = (row.req_qty * (row.unit_price || 0));
        },

        addBomRow() {
            const newRow = {
                bom_item_id: '',
                sales_order_item_id: '',
                garment_qty: 0,
                consumption: 0,
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
            row.bom_item_id = '';
            row.sales_order_item_id = '';
            row.garment_qty = 0;
            row.consumption = 0;
            row.req_qty = 0;
            row.unit_price = 0;
            row.total_cost = 0;
        },

        get grandTotalBudget() {
            return this.items.reduce((sum, row) => sum + (parseFloat(row.total_cost) || 0), 0);
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
</script>
@endpush