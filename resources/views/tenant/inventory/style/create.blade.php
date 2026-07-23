@extends('layouts.tenant')
@section('title','Stock Adjustment')
@section('content')
<!-- Initializing the root Alpine application data layer -->
<div x-data="stockAdjustmentApp()" class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- টপবার এবং মোড টগল বোতাম -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Style Creation</h4>
            <p class="text-xs text-slate-400 mt-0.5">Receive or adjust physical inventory inside tenant warehouses.</p>
        </div>
    </div>

    <!-- Prevent standard submission to handle it asynchronously via our script -->
    <form @submit.prevent="submitForm" class="p-6 space-y-5">
        <!-- ইনফরমেশন মেটা ব্লক -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Style Name</label>
                <input type="text" x-model="styleName" placeholder="NOVOJKT-02" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 font-bold font-mono focus:outline-none focus:border-indigo-500">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Style SKU</label>
                <!-- Pre-filled via standard Alpine state initialization -->
                <input type="text" x-model="styleSku" readonly class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-bold font-mono outline-none">
            </div>
        </div>

        <!-- বাল্ক আইটেম গ্রিড টেবিল -->
        <div class="space-y-3">
            <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 w-3.5/12">Master Item</th>
                            <th class="p-3 w-1.5/12">Color</th>
                            <th class="p-3 w-1/12">Size</th>
                            <th class="p-3 w-2.5/12">Warehouse Destination</th>
                            <th class="p-3 w-1/12 text-right">Qty</th>
                            <th class="p-3 w-1/12 text-right">Cost</th>
                            <th class="p-3 w-1/12 text-right">Total</th>
                            <th class="p-3 w-0.5/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                        <!-- Dynamic loop generation with index keys for fast execution tracking -->
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <!-- 1. Master Item Input -->
                                <td class="p-2.5 pl-4">
                                    <input type="text" x-model="item.item_name" placeholder="Item name or code..." class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-left font-medium text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>
                                
                                <!-- 2. Color Dropdown -->
                                <td class="p-2.5">
                                    <select x-model="item.color_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Color</option>
                                        @foreach($colors as $key => $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                <!-- 3. Size Dropdown -->
                                <td class="p-2.5">
                                    <select x-model="item.size_id" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="">Select Size</option>
                                        @foreach($units as $key => $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}-{{ $unit->short_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                <!-- 4. Warehouse Destination Dropdown -->
                                <td class="p-2.5">
                                    <select x-model="item.warehouse" class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                                        <option value="wh-2">Central Warehousing Unit-2</option>
                                        <option value="wh-1">Primary Depot Unit-1</option>
                                    </select>
                                </td>
                                
                                <!-- 5. Quantity Field (x-model.number casts standard inputs to float/int metrics) -->
                                <td class="p-2.5">
                                    <input type="number" min="1" placeholder="0" x-model.number="item.qty" class="qty-input w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>

                                <!-- 6. Cost Field -->
                                <td class="p-2.5">
                                    <input type="number" min="0" step="0.01" placeholder="0.00" x-model.number="item.cost" class="cost-input w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                </td>

                                <!-- 7. Total Field (Auto-calculated via internal object values dynamically) -->
                                <td class="p-2.5">
                                    <input type="text" :value="((item.qty || 0) * (item.cost || 0)).toFixed(2)" readonly class="total-input w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-right font-mono font-bold text-slate-500 focus:outline-none">
                                </td>
                                
                                <!-- 8. Action Trigger Button -->
                                <td class="p-2.5 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg transition">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <!-- Running Calculation Totals Footer tied directly to Alpine computed getters -->
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-700 text-xs">
                            <td colspan="4" class="p-3 pl-4 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">Grand Aggregates:</td>
                            <td class="p-3 px-2 text-right font-mono text-indigo-600" x-text="grandQty"></td>
                            <td class="p-3 px-2 text-right font-mono text-slate-500" x-text="avgCost"></td>
                            <td class="p-3 px-2 text-right font-mono text-emerald-600 pr-10" x-text="grandTotal"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50/50 px-3 py-2 rounded-xl transition border border-dashed border-indigo-200">
                + Add Item Line
            </button>
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Internal Remarks / Memo</label>
            <input type="text" x-model="remarks" placeholder="Optional notes regarding this batch..." class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800">
        </div>

        <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <button type="button" @click="resetForm" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</button>
            <button type="submit" :disabled="isSaving" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-400 rounded-xl shadow-sm transition">
                <span x-text="isSaving ? 'Processing Ledger...' : 'Post Stock Ledger'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function stockAdjustmentApp() {
    return {
        styleName: '',
        styleSku: 'STK-20260709-001', // Keeps your structural default fallback string intact
        remarks: '',
        isSaving: false,
        items: [
            // Standard initial model seed
            { item_name: '', color_id: '', size_id: '', warehouse: 'wh-2', qty: '', cost: '' }
        ],

        // Row Operations
        addItem() {
            this.items.push({ item_name: '', color_id: '', size_id: '', warehouse: 'wh-2', qty: '', cost: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            } else {
                alert("Your tracking grid must maintain at least one transactional input line.");
            }
        },
        resetForm() {
            if(confirm("Are you sure you want to discard changes?")) {
                location.reload();
            }
        },

        // Reactive Summary Getters
        get grandQty() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
        },
        get grandTotal() {
            const total = this.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.cost) || 0)), 0);
            return total.toFixed(2);
        },
        get avgCost() {
            const linesWithCost = this.items.filter(item => parseFloat(item.cost) > 0);
            if (linesWithCost.length === 0) return '0.00';
            const sumCost = linesWithCost.reduce((sum, item) => sum + parseFloat(item.cost), 0);
            return (sumCost / linesWithCost.length).toFixed(2);
        },

        // Post Submissions Form handling
        submitForm() {
            if (!this.styleName.trim()) {
                alert("Please add a Style Reference Name before executing records creation.");
                return;
            }

            // Verify if any items inside array lack mandatory entries
            const hasIncompleteLines = this.items.some(item => 
                !item.item_name.trim() || !item.color_id || !item.size_id || !item.qty || !item.cost
            );

            if (hasIncompleteLines) {
                alert("Please fully complete item information across all fields on input lines.");
                return;
            }

            this.isSaving = true;

            // Route point should point to your target controller method endpoint
            fetch("{{ route('tenant.inventory.stock.style-with-items-save') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    style_name: this.styleName,
                    style_sku: this.styleSku,
                    remarks: this.remarks,
                    items: this.items
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isSaving = false;
                if (data.success) {
                    alert(data.message || "Stock adjustments loaded cleanly into architecture database.");
                    location.reload();
                } else {
                    alert("Processing Execution Errors:\n" + data.errors.join("\n"));
                }
            })
            .catch(error => {
                this.isSaving = false;
                console.error('System Pipeline Failure:', error);
                alert("An unexpected transport layer connection block occurred.");
            });
        }
    }
}
</script>
@endpush