@extends('layouts.tenant')
@section('title', isset($supplier) ? 'Edit Supplier' : 'Supplier Entry')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'supplier-form', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'supplier-form'" x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">{{ isset($supplier) ? 'Edit Supplier / Vendor' : 'Add New Supplier / Vendor' }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                    {{ isset($supplier) ? 'Modify existing vendor records and details.' : 'Register a new vendor to manage purchase orders and GRN.' }}
                </p>
            </div>

            <form action="{{ isset($supplier) ? route('tenant.purchase.suppliers.update', $supplier->id) : route('tenant.purchase.suppliers.store') }}" method="POST" class="space-y-6">
                @csrf

                @if(isset($supplier))
                    @method('PUT')
                @endif

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">1. Company Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier / Company Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $supplier->name ?? '') }}"
                                placeholder="e.g. Acme Logistics Ltd."
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Code (Auto/Manual)</label>
                            <input type="text" name="supplier_code" id="supplier_code"
                                value="{{ old('supplier_code', $supplier->supplier_code ?? $suggestedCode) }}"
                                placeholder="SUP-9THNT"
                                class="w-full rounded-lg border border-gray-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-slate-400 mt-1">Leave as-is to auto-generate, or type your own code.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tax / BIN Number</label>
                            <input type="text" name="tax_id" id="tax_id"
                                value="{{ old('tax_id', $supplier->tax_id ?? '') }}"
                                placeholder="e.g. BIN-11223344"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Company Address</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $supplier->address ?? '') }}"
                                placeholder="Street, City, Country"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">2. Contact Person Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Name <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_person" id="contact_person" required
                                value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                                placeholder="John Doe"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contact_person') border-red-400 @enderror">
                            @error('contact_person') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address<span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $supplier->email ?? '') }}"
                                placeholder="john@supplier.com"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number<span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone" required
                                value="{{ old('phone', $supplier->phone ?? '') }}"
                                placeholder="+880 17XX XXXXXX"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">3. Payment Terms & Bank Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Payment Terms</label>
                            <select name="payment_terms_days" id="payment_terms_days"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($paymentTerms as $days => $label)
                                    <option value="{{ $days }}" @selected(old('payment_terms_days', $supplier->payment_terms_days ?? 30) == $days)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name"
                                value="{{ old('bank_name', $supplier->bank_name ?? '') }}"
                                placeholder="e.g. City Bank PLC"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bank Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number"
                                value="{{ old('bank_account_number', $supplier->bank_account_number ?? '') }}"
                                placeholder="1102XXXXXXXXX"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100 gap-2">
                    <a href="{{ route('tenant.purchase.suppliers') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none">
                        {{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection