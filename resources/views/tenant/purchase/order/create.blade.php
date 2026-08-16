@extends('layouts.tenant')
@section('title', 'Create Purchase Order')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="poForm()">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Create Raw Material PO</h2>
            <p class="text-xs text-slate-400 mt-0.5">Generate supplier booking order from MPR requirements.</p>
        </div>
        <a href="#" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
            &larr; Back to MPR
        </a>
    </div>

    <!-- PO Form Starts -->
    <form action="#" method="POST" class="space-y-6">
        @csrf

        <!-- SECTION 1: PO Header & Metadata -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                1. Order & Supplier Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Supplier Selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Select Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" required class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">-- Choose Supplier --</option>
                        {{-- Dummy Data for UI preview --}}
                        <option value="1">Pacific Accessories Ltd.</option>
                        <option value="2">Apex Textile Mills</option>
                    </select>
                </div>

                <!-- PO Number (Auto Generated) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PO Number</label>
                    <input type="text" name="po_number" value="PO-2026-0001" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs p-2.5 text-gray-500 font-mono font-semibold">
                </div>

                <!-- PO Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PO Date <span class="text-red-500">*</span></label>
                    <input type="date" name="po_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- Expected Delivery Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Expected Delivery Date</label>
                    <input type="date" name="delivery_date" class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Material Items Matrix (Dynamic Table) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">
                    2. Materials Booking Table
                </h3>
                <button type="button" @click="addItem()" class="px-3 py-1.5 text-xs font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-all">
                    + Add Extra Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <th class="p-3 w-1/4">Item Description</th>
                            <th class="p-3">Color</th>
                            <th class="p-3">Size</th>
                            <th class="p-3 text-right">MPR Req.</th>
                            <th class="p-3 text-right w-28">Booking Qty</th>
                            <th class="p-3 text-center w-20">Unit</th>
                            <th class="p-3 text-right w-28">Unit Price ($)</th>
                            <th class="p-3 text-right w-32">Total ($)</th>
                            <th class="p-3 text-center w-12">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <!-- Item Name -->
                                <td class="p-2">
                                    <input type="text" x-model="item.name" :name="`items[${index}][item_name]`" class="w-full border border-gray-200 rounded p-1.5 focus:border-indigo-500 font-semibold text-slate-800">
                                </td>
                                <!-- Color -->
                                <td class="p-2">
                                    <input type="text" x-model="item.color" :name="`items[${index}][color]`" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <!-- Size -->
                                <td class="p-2">
                                    <input type="text" x-model="item.size" :name="`items[${index}][size]`" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <!-- MPR Required (Readonly) -->
                                <td class="p-2 text-right font-mono text-gray-500 font-bold" x-text="item.mpr_qty"></td>
                                <!-- Order/Booking Qty (Editable) -->
                                <td class="p-2">
                                    <input type="number" step="0.01" x-model.number="item.order_qty" :name="`items[${index}][order_qty]`" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono font-bold text-indigo-600 focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <!-- Unit -->
                                <td class="p-2">
                                    <input type="text" x-model="item.unit" :name="`items[${index}][unit]`" class="w-full border border-gray-200 rounded p-1.5 text-center font-mono">
                                </td>
                                <!-- Unit Price -->
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <!-- Total Amount -->
                                <td class="p-2 text-right font-mono font-bold text-slate-800" x-text="'$' + (item.order_qty * item.unit_price).toFixed(2)"></td>
                                <!-- Remove Row -->
                                <td class="p-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 font-bold text-base">&times;</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 3: Summary, Notes & Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Instructions / Terms -->
            <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Special Instructions / Terms & Conditions</label>
                <textarea name="notes" rows="4" placeholder="e.g. Fabric must be pre-shrunk. Packaging instructions..." class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <!-- Grand Total Card -->
            <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block mb-1">Total Booking Value</span>
                    <div class="text-3xl font-extrabold font-mono text-emerald-400" x-text="'$' + grandTotal().toFixed(2)">$0.00</div>
                    <p class="text-[10px] text-slate-400 mt-2">Calculated automatically based on qty & prices.</p>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" name="status" value="draft" class="w-1/2 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg border border-slate-700 transition-all">
                        Save Draft
                    </button>
                    <button type="submit" name="status" value="approved" class="w-1/2 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg shadow-md transition-all">
                        Submit PO
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Alpine.js Dynamic Logic -->
<script>
    function poForm() {
        return {
            // MPR থেকে আসা আইটেমগুলোর মক ডাটা (Controller থেকে pass করা হবে)
            items: [
                { name: '100% Cotton Single Jersey Fabric', color: 'Navy Blue', size: 'All', mpr_qty: 1200.00, order_qty: 1250.00, unit: 'Kgs', unit_price: 3.50 },
                { name: '1x1 Rib Collar', color: 'Navy Blue', size: 'M', mpr_qty: 450.00, order_qty: 450.00, unit: 'Pcs', unit_price: 0.25 },
                { name: 'Main Woven Label', color: 'Main', size: 'Freesize', mpr_qty: 5000.00, order_qty: 5200.00, unit: 'Pcs', unit_price: 0.04 }
            ],
            addItem() {
                this.items.push({ name: '', color: '', size: '', mpr_qty: 0, order_qty: 0, unit: 'Pcs', unit_price: 0 });
            },
            removeItem(index) {
                if(this.items.length > 1) {
                    this.items.splice(index, 1);
                } else {
                    alert('At least one item is required!');
                }
            },
            grandTotal() {
                return this.items.reduce((total, item) => total + (item.order_qty * item.unit_price), 0);
            }
        }
    }
</script>
@endsection