@extends('layouts.tenant')
@section('title', isset($po) ? 'Edit Purchase Order' : 'Create Purchase Order')
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
    /* Hide Number Input Spinners for cleaner UI */
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
{{--
    এই একই ফর্ম Create ও Edit দুটোর জন্যই ব্যবহার হয়।
    Edit করার সময় controller থেকে $po (with items, services loaded) পাস করে দিন।
    Create করার সময় $po পাঠানোর দরকার নেই / null রাখলেই চলবে।
--}}
<div class="max-w-7xl mx-auto space-y-6"
     x-data="poForm(@json($po ?? null))">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Edit Purchase Order' : 'Create Raw Material PO'"></h2>
            <p class="text-xs text-slate-400 mt-0.5">Generate supplier booking order from MPR requirements.</p>
        </div>
        <a href="{{ route('tenant.merch.styles') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
            &larr; Back to MPR
        </a>
    </div>

    <!-- Loading indicator while MPR items are fetched -->
    <div x-show="loading" x-cloak class="text-xs font-semibold text-indigo-600 px-1">
        Loading MPR items...
    </div>

    <!-- PO Form Starts -->
    <form @submit.prevent class="space-y-6">
        @csrf

        <!-- PO ID kept in sync with Alpine state for edit mode -->
        <input type="hidden" name="po_id" :value="poId">

        <!-- SECTION 1: PO Header & Metadata -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                1. Order & Supplier Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Style Selection Dropdown -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Select Style / Order <span class="text-red-500">*</span></label>
                    <select x-ref="styleSelect"
                            name="style_id"
                            required
                            class="w-full text-xs"
                            x-init="$nextTick(() => initStyleSelect2())">
                        <option value=""></option>
                        @foreach($styles as $style)
                            <option value="{{ $style->id }}" @selected(isset($po) && (string) $po->style_id === (string) $style->id)>
                                {{ $style->style_number ?? $style->code }} {{ isset($style->product_name) ? "({$style->product_name})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Selection Dropdown -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Select Supplier <span class="text-red-500">*</span></label>
                    <select x-ref="supplierSelect"
                            name="supplier_id"
                            required
                            class="w-full text-xs"
                            x-init="$nextTick(() => initSupplierSelect2())">
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" @selected(isset($po) && (string) $po->supplier_id === (string) $supplier->id)>
                                {{ $supplier->name }} ({{ strtoupper($supplier_types[$supplier->supplier_type] ?? 'General') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PO Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PO Date <span class="text-red-500">*</span></label>
                    <input type="date" name="po_date"
                           value="{{ isset($po) ? \Illuminate\Support\Carbon::parse($po->po_date)->format('Y-m-d') : date('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- Expected Delivery Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Expected Delivery Date</label>
                    <input type="date" name="delivery_date"
                           value="{{ isset($po) && $po->delivery_date ? \Illuminate\Support\Carbon::parse($po->delivery_date)->format('Y-m-d') : '' }}"
                           class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Material Items Matrix (Dynamic Table) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">
                    2. Materials Booking Table
                </h3>
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
                                <!-- Keeps the linked material's identity across create/edit -->
                                <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id || ''">

                                <td class="p-2">
                                    <input type="text" x-model="item.name" :name="`items[${index}][item_name]`" class="w-full border border-gray-200 rounded p-1.5 focus:border-indigo-500 font-semibold text-slate-800">
                                </td>
                                <td class="p-2">
                                    <input type="text" x-model="item.color" :name="`items[${index}][color]`" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <td class="p-2">
                                    <input type="text" x-model="item.size" :name="`items[${index}][size]`" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <td class="p-2 text-right font-mono text-gray-500 font-bold" x-text="item.mpr_qty"></td>
                                <td class="p-2">
                                    <input type="number" step="0.01" x-model.number="item.order_qty" :name="`items[${index}][order_qty]`" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono font-bold text-indigo-600 focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="text" x-model="item.unit" :name="`items[${index}][unit]`" class="w-full border border-gray-200 rounded p-1.5 text-center font-mono">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="p-2 text-right font-mono font-bold text-slate-800" x-text="'$' + ((item.order_qty || 0) * (item.unit_price || 0)).toFixed(2)"></td>
                                <td class="p-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 font-bold text-base">&times;</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="9" class="p-4 text-center text-gray-400 italic">
                                Please select Style & Supplier to load MPR requirements.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 3: Summary, Notes & Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Special Instructions / Terms & Conditions</label>
                <textarea name="notes" rows="4" x-model="notes" placeholder="e.g. Fabric must be pre-shrunk. Packaging instructions..." class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block mb-1">Total Booking Value</span>
                    <div class="text-3xl font-extrabold font-mono text-emerald-400" x-text="'$' + grandTotal().toFixed(2)">$0.00</div>
                    <p class="text-[10px] text-slate-400 mt-2">Calculated automatically based on qty & prices.</p>
                </div>

                <div class="flex gap-2 mt-6">
                    <!-- type="button" ব্যবহার করা হয়েছে ইচ্ছাকৃতভাবে, যাতে native
                         submit-এর সাথে race condition না হয়। status এখানেই ঠিক করে
                         submitForm() কল করা হচ্ছে। -->
                    <button type="button"
                            :disabled="isSaving"
                            @click="submitForm('draft')"
                            class="w-1/2 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg border border-slate-700 transition-all disabled:opacity-50">
                        <span x-text="isSaving && pendingStatus === 'draft' ? 'Saving...' : 'Save Draft'"></span>
                    </button>
                    <button type="button"
                            :disabled="isSaving"
                            @click="submitForm('approved')"
                            class="w-1/2 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg shadow-md transition-all disabled:opacity-50">
                        <span x-text="isSaving && pendingStatus === 'approved' ? 'Processing...' : 'Submit PO'"></span>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
    function poForm(initialPo) {
        return {
            // --- Core state ---
            isEdit: !!initialPo,
            poId: initialPo ? initialPo.id : null,
            selectedStyleId: initialPo ? String(initialPo.style_id ?? '') : '',
            selectedSupplierId: initialPo ? String(initialPo.supplier_id ?? '') : '',
            notes: initialPo ? (initialPo.notes ?? '') : '',
            items: initialPo && Array.isArray(initialPo.items) ? initialPo.items.map(i => ({
                item_id: i.item_id ?? i.item_id ?? null,
                name: i.item_name ?? i.name ?? '',
                color: i.color ?? '',
                size: i.size ?? '',
                mpr_qty: i.mpr_qty ?? 0,
                order_qty: Number(i.order_qty ?? i.qty ?? 0),
                unit: i.unit ?? '',
                unit_price: Number(i.unit_price ?? i.cost ?? 0),
            })) : [],
            loading: false,
            isSaving: false,
            pendingStatus: null,

            // Guards fetchMprItems() from wiping out items that were just
            // hydrated from the server when editing an existing PO.
            hydrating: !!initialPo,

            // Init Select2 on the Style dropdown and wire its change event
            // back into THIS component's state (no nested x-data, so `this`
            // correctly refers to the poForm() scope).
            initStyleSelect2() {
                let el = $(this.$refs.styleSelect);

                if (el.data('select2')) {
                    el.select2('destroy');
                }
                el.removeAttr('data-select2-id').removeData('select2-id');

                el.select2({
                    width: '100%',
                    placeholder: '-- Choose Style --',
                    allowClear: true
                }).on('change', (e) => {
                    let selectedVal = e.target.value;
                    this.selectedStyleId = selectedVal ? String(selectedVal) : '';
                    this.fetchMprItems();
                });
            },

            // Init Select2 on the Supplier dropdown, same pattern as above.
            initSupplierSelect2() {
                let el = $(this.$refs.supplierSelect);

                if (el.data('select2')) {
                    el.select2('destroy');
                }
                el.removeAttr('data-select2-id').removeData('select2-id');

                el.select2({
                    width: '100%',
                    placeholder: '-- Choose Supplier --',
                    allowClear: true
                }).on('change', (e) => {
                    let selectedVal = e.target.value;
                    this.selectedSupplierId = selectedVal ? String(selectedVal) : '';
                    this.fetchMprItems();
                });

                // Both dropdowns are initialized by this point (style first,
                // then supplier via x-init order in the DOM), so this is a
                // safe place to release the hydration guard.
                this.$nextTick(() => { this.hydrating = false; });
            },

            fetchMprItems() {
                // Edit mode-এ পেজ প্রথম লোড হওয়ার সময় select2 init হতে গিয়ে
                // change event trigger করলেও, ইতিমধ্যে সার্ভার থেকে আসা items
                // মুছে যাবে না — শুধু ইউজার সত্যিকারের dropdown পরিবর্তন করলেই fetch হবে।
                if (this.hydrating) {
                    return;
                }

                if (!this.selectedStyleId || !this.selectedSupplierId) {
                    this.items = [];
                    return;
                }

                this.loading = true;

                let url = "{{ route('tenant.api.get-mpr-items', [
                    'style_id' => '__sid',
                    'supplier_id' => '__supid',
                ]) }}";

                url = url.replace('__sid', this.selectedStyleId).replace('__supid', this.selectedSupplierId);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`Request failed with status ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.items = data;
                    })
                    .catch(err => {
                        console.error('Failed to load MPR items:', err);
                        this.items = [];
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            grandTotal() {
                return this.items.reduce((total, item) => total + ((item.order_qty || 0) * (item.unit_price || 0)), 0);
            },

            submitForm(status) {
                const styleId    = String(this.selectedStyleId || '').trim();
                const supplierId = String(this.selectedSupplierId || '').trim();

                if (!styleId || !supplierId) {
                    alert("Please complete all required fields (*).");
                    return;
                }

                this.pendingStatus = status;
                this.isSaving = true;

                // Edit হলে update route (PO id সহ), নাহলে store route
                let url = this.isEdit
                    ? "{{ route('tenant.purchase.po.update', ['id' => '__poid']) }}".replace('__poid', this.poId)
                    : "{{ route('tenant.purchase.po.store') }}";

                let formData = new FormData();
                formData.append('style_id', styleId);
                formData.append('supplier_id', supplierId);
                formData.append('status', status);
                formData.append('notes', this.notes || '');
                formData.append('po_date', document.querySelector('input[name="po_date"]').value || '');
                formData.append('delivery_date', document.querySelector('input[name="delivery_date"]').value || '');

                console.log(this.items);

                this.items.forEach((item, index) => {
                    formData.append(`items[${index}][item_id]`, item.item_id ?? '');
                    formData.append(`items[${index}][item_name]`, item.name || '');
                    formData.append(`items[${index}][color]`, item.color || '');
                    formData.append(`items[${index}][size]`, item.size || '');
                    formData.append(`items[${index}][order_qty]`, item.order_qty || 0);
                    formData.append(`items[${index}][unit]`, item.unit || '');
                    formData.append(`items[${index}][unit_price]`, item.unit_price || 0);
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
                    // ✅ FIX: error path-এও isSaving reset হয়, বাটন চিরকাল
                    // disabled থেকে যায় না
                    this.isSaving = false;

                    if (!data) return;

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
        }
    }
</script>
@endsection