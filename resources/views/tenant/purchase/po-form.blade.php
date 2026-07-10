@extends('layouts.tenant')
@section('title','Purchase Order Form')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'purchase-order', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Create Purchase Order</h2>
            </div>

            <form action="{{ route('tenant.purchase.store') }}" method="POST" class="p-6 space-y-5 text-xs overflow-y-auto">
                @csrf

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex gap-3 items-start animate-shake">
                        <span class="text-base mt-0.5">⚠️</span>
                        <div>
                            <p class="font-bold mb-1 text-sm">Action Required:</p>
                            <ul class="list-disc pl-4 space-y-0.5 font-medium text-rose-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Header/Meta Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-gray-100 pb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Order #</label>
                        <input type="text" name="po_number" value="PO-{{ Str::upper(Str::random(6)) }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PO Date</label>
                        <input type="date" name="po_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Terms</label>
                        <select name="payment_terms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option>Net 30</option>
                            <option>Net 60</option>
                            <option>COD</option>
                            <option>Due on Receipt</option>
                        </select>
                    </div>
                </div>

                <!-- Vendor & Shipping Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100 pb-6">
                    <!-- Vendor Box -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 text-indigo-600">Vendor Info</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Select Supplier</label>
                                <select name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a vendor...</option>
                                    <!-- Loop suppliers here -->
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Delivery Notes</label>
                                <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Any specific requirements..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Box -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 text-indigo-600">Ship To</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Shipping Address / Warehouse</label>
                                <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option>Main Warehouse</option>
                                    <option>Branch Office B</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Expected Delivery Date</label>
                                <input type="date" name="expected_delivery" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Itemized Table -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-3">Order Line Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">SKU / Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Description</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Unit Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="p-2">
                                            <input type="text" x-model="item.sku" :name="'items['+index+'][sku]'" placeholder="SKU-001" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" x-model="item.description" :name="'items['+index+'][description]'" placeholder="Item description..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" x-model.number="item.qty" :name="'items['+index+'][qty]'" min="1" class="block w-full text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </td>
                                        <td class="p-2">
                                            <div class="relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">$</span>
                                                </div>
                                                <input type="number" x-model.number="item.price" :name="'items['+index+'][price]'" step="0.01" min="0" class="block w-full pl-7 text-right rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                                            </div>
                                        </td>
                                        <td class="p-2 text-right text-sm font-medium text-gray-900 pr-4">
                                            $<span x-text="calculateRowTotal(item).toFixed(2)"></span>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900 focus:outline-none" :disabled="items.length === 1">
                                                &times;
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 border border-dashed border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            + Add Another Row
                        </button>
                    </div>
                </div>

                <!-- Calculation Calculations / Totals -->
                <div class="flex justify-end pt-6 border-t border-gray-100">
                    <div class="w-full md:w-1/3 space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-medium">$<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-600 items-center">
                            <span>Tax Rate (%):</span>
                            <input type="number" x-model.number="taxRate" class="w-20 text-right rounded-md border-gray-300 py-1 sm:text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax Amount:</span>
                            <span class="font-medium">$<span x-text="taxTotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-600 items-center">
                            <span>Shipping/Handling:</span>
                            <input type="number" x-model.number="shipping" class="w-24 text-right rounded-md border-gray-300 py-1 sm:text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex justify-between text-gray-900 text-base font-bold pt-3 border-t border-gray-200">
                            <span>Grand Total:</span>
                            <span>$<span x-text="grandTotal.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Form Submissions Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Save Draft
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none shadow-sm">
                        Submit Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection