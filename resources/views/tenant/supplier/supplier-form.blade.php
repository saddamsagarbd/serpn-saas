@extends('layouts.tenant')
@section('title','Supplier Entry')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'purchase-order', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">Add New Supplier / Vendor</h3>
                <p class="text-xs text-gray-500 mt-1">Register a new vendor to manage purchase orders and GRN.</p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                @csrf

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">1. Company Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier / Company Name *</label>
                            <input type="text" required placeholder="e.g. Acme Logistics Ltd." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Code (Auto/Manual)</label>
                            <input type="text" value="SUP-{{ strtoupper(Str::random(5)) }}" readonly class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-600 font-mono text-sm py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tax / BIN Number</label>
                            <input type="text" placeholder="e.g. BIN-11223344" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Company Address</label>
                            <input type="text" placeholder="Street, City, Country" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">2. Contact Person Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Name *</label>
                            <input type="text" required placeholder="John Doe" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" required placeholder="john@supplier.com" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" required placeholder="+880 17XX XXXXXX" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">3. Payment Terms & Bank Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Payment Terms</label>
                            <select class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                <option>Net 30 Days</option>
                                <option>Net 60 Days</option>
                                <option>COD (Cash on Delivery)</option>
                                <option>Advance Payment</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name</label>
                            <input type="text" placeholder="e.g. City Bank PLC" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bank Account Number</label>
                            <input type="text" placeholder="1102XXXXXXXXX" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100 gap-2">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none">
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection