@extends('layouts.tenant')
@section('title', isset($orders) ? 'Edit Purchase Order' : 'Create Purchase Order')

@push('styles')
<style>
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
<div class="max-w-7xl mx-auto space-y-6" x-data='poForm(@json($orders ?? null))'>

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Edit Purchase Order' : 'Create Raw Material PO'"></h2>
            <p class="text-xs text-slate-400 mt-0.5">Generate supplier booking order from MPR requirements.</p>
        </div>
        <a href="{{ route('tenant.merch.mpr.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
            &larr; Back to MPR
        </a>
    </div>

    <!-- Loading Indicator -->
    <div x-show="loading" x-cloak class="text-xs font-semibold text-indigo-600 px-1">
        Loading MPR items...
    </div>

    <!-- PO Form Starts -->
    <form @submit.prevent class="space-y-6">
        @csrf
        <input type="hidden" name="po_id" :value="poId">

        <!-- SECTION 1: Order & Supplier Details -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                1. Order & Supplier Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Style Selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Select Style / Order <span class="text-red-500">*</span></label>
                    <select x-ref="styleSelect" name="style_id" required class="w-full text-xs" x-init="$nextTick(() => initStyleSelect2())">
                        <option value=""></option>
                        @foreach($styles as $style)
                            <option value="{{ $style->id }}" @selected(isset($orders) && (string) $orders->style_id === (string) $style->id)>
                                {{ $style->style_number ?? $style->code }} {{ isset($style->product_name) ? "({$style->product_name})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Select Supplier <span class="text-red-500">*</span></label>
                    <select x-ref="supplierSelect" name="supplier_id" required class="w-full text-xs" x-init="$nextTick(() => initSupplierSelect2())">
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" @selected(isset($orders) && (string) $orders->supplier_id === (string) $supplier->id)>
                                {{ $supplier->name }} ({{ strtoupper($supplier_types[$supplier->supplier_type] ?? 'General') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PO Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PO Date <span class="text-red-500">*</span></label>
                    <input type="date" name="po_date" x-model="poDate" class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <!-- Delivery Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Expected Delivery Date</label>
                    <input type="date" name="delivery_date" x-model="deliveryDate" class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Material Items Matrix -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                2. Materials Booking Table
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <th class="p-3 w-1/4">Item Description</th>
                            <th class="p-3">Color</th>
                            <th class="p-3">Size</th>
                            <th class="p-3 text-right">GMT Req.</th>
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
                                <td class="p-2">
                                    <input type="text" x-model="item.name" class="w-full border border-gray-200 rounded p-1.5 font-semibold text-slate-800">
                                </td>
                                <td class="p-2">
                                    <input type="text" x-model="item.color" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <td class="p-2">
                                    <input type="text" x-model="item.size" class="w-full border border-gray-200 rounded p-1.5">
                                </td>
                                <td class="p-2 text-right font-mono text-gray-500 font-bold" x-text="item.mpr_qty"></td>
                                <td class="p-2">
                                    <input type="number" step="0.01" x-model.number="item.order_qty" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono font-bold text-indigo-600 focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="p-2">
                                    <input type="text" readonly x-model="item.unit" class="w-full border border-gray-200 rounded p-1.5 text-center font-mono bg-gray-50">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.0001" x-model.number="item.unit_price" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono focus:ring-1 focus:ring-indigo-500">
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

        <!-- SECTION 3: Additional Bills & Summary Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Payment Terms & Remarks -->
            <div class="md:col-span-1 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Terms</label>
                    <input type="text" x-model="paymentTermsText" placeholder="e.g. 60% Advance, 40% After Delivery" class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Remarks / Special Instructions</label>
                    <textarea rows="4" x-model="remarks" placeholder="Fabric must be pre-shrunk..." class="w-full border border-gray-300 rounded-lg text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <!-- Middle: Extra Cost Matrix -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-gray-700 uppercase border-b pb-2">Additional Expenses</h4>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block text-gray-600 mb-0.5">Transport Cost</label>
                        <input type="number" step="0.01" x-model.number="transportCost" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-0.5">Loader Bill</label>
                        <input type="number" step="0.01" x-model.number="loaderBill" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-0.5">Inspection Bill</label>
                        <input type="number" step="0.01" x-model.number="inspectionBill" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-0.5">Extra Charges</label>
                        <input type="number" step="0.01" x-model.number="extraCharges" class="w-full border border-gray-300 rounded p-1.5 text-right font-mono">
                    </div>
                    <div class="col-span-2 border-t pt-2">
                        <label class="block text-gray-600 mb-0.5">Discount Amount (-)</label>
                        <input type="number" step="0.01" x-model.number="discount" class="w-full border border-red-300 text-red-600 rounded p-1.5 text-right font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- Right: Billing Calculation -->
            <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span class="text-slate-400">Items Subtotal:</span>
                        <span class="font-mono font-bold text-slate-200" x-text="'$' + subtotal().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span class="text-slate-400">Extra Charges:</span>
                        <span class="font-mono text-slate-300" x-text="'+$' + additionalCosts().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-red-400">
                        <span>Discount:</span>
                        <span class="font-mono" x-text="'-$' + (discount || 0).toFixed(2)"></span>
                    </div>
                    <div class="pt-2">
                        <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block mb-1">Grand Total Value</span>
                        <div class="text-3xl font-extrabold font-mono text-emerald-400" x-text="'$' + grandTotal().toFixed(2)"></div>
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="button" :disabled="isSaving" @click="submitForm('draft')" class="w-1/2 py-2.5 text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg border border-slate-700 transition-all disabled:opacity-50">
                        <span x-text="isSaving && pendingStatus === 'draft' ? 'Saving...' : 'Save Draft'"></span>
                    </button>
                    <button type="button" :disabled="isSaving" @click="submitForm('approved')" class="w-1/2 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg shadow-md transition-all disabled:opacity-50">
                        <span x-text="isSaving && pendingStatus === 'approved' ? 'Processing...' : 'Submit PO'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function poForm(initialPo) {
        // Date formatting helper function (YYYY-MM-DD)
        const formatDate = (dateStr) => {
            if (!dateStr) return '';
            return dateStr.split('T')[0].split(' ')[0];
        };

        // Get array of items safely from Eloquent relation
        const extractItems = (po) => {
            if (!po) return [];
            
            // Controller থেকে $orders->order রিলেশন পাঠানো হয়েছে
            const rawItems = po.order || po.items || [];
            if (!Array.isArray(rawItems)) return [];

            console.log(rawItems);

            return rawItems.map(i => ({
                item_id: i.item_id ?? null,
                // item রিলেশন থেকে নাম নেওয়া হচ্ছে, না থাকলে item_name
                name: i.item ? i.item.name : (i.item_name ?? ''),
                color: i.color ? i.color.name : (i.color ?? 'N/A'),
                color_id: i.color_id ?? '',
                size_id: i.size_id ?? '',
                size: i.size ? i.size.name : (i.size ?? 'N/A'),
                mpr_qty: Number(i.mpr_qty ?? 0),
                order_qty: Number(i.order_qty ?? 0),
                unit: i.unit ? i.unit.name : (i.unit ?? 'Pcs'),
                unit_id: i.unit_id ?? '',
                unit_price: Number(i.unit_price ?? 0),
            }));
        };

        console.log(initialPo.po_date);

        return {
            isEdit: !!initialPo,
            poId: initialPo ? initialPo.id : null,
            selectedStyleId: initialPo ? String(initialPo.style_id ?? '') : '',
            selectedSupplierId: initialPo ? String(initialPo.supplier_id ?? '') : '',
            poDate: initialPo ? formatDate(initialPo.po_date) : '{{ date("Y-m-d") }}',
            deliveryDate: initialPo ? formatDate(initialPo.delivery_date) : '',
            
            // Charges Mapping with DB Schema
            transportCost: initialPo ? Number(initialPo.transport_cost ?? 0) : 0,
            loaderBill: initialPo ? Number(initialPo.loader_bill ?? 0) : 0,
            inspectionBill: initialPo ? Number(initialPo.inspection_bill ?? 0) : 0,
            extraCharges: initialPo ? Number(initialPo.extra_charges ?? 0) : 0,
            discount: initialPo ? Number(initialPo.discount ?? 0) : 0,
            
            paymentTermsText: initialPo ? (initialPo.payment_terms_text ?? '') : '',
            remarks: initialPo ? (initialPo.remarks ?? '') : '',

            items: extractItems(initialPo),
            loading: false,
            isSaving: false,
            pendingStatus: null,
            hydrating: !!initialPo,

            initStyleSelect2() {
                let el = $(this.$refs.styleSelect);
                if (el.data('select2')) el.select2('destroy');
                el.select2({ width: '100%', placeholder: '-- Choose Style --', allowClear: true });

                // Sync initial value for Edit mode
                if (this.selectedStyleId) {
                    el.val(this.selectedStyleId).trigger('change.select2');
                }

                el.on('change', (e) => {
                    this.selectedStyleId = e.target.value ? String(e.target.value) : '';
                    this.fetchMprItems();
                });
            },

            initSupplierSelect2() {
                let el = $(this.$refs.supplierSelect);
                if (el.data('select2')) el.select2('destroy');
                el.select2({ width: '100%', placeholder: '-- Choose Supplier --', allowClear: true });

                // Sync initial value for Edit mode
                if (this.selectedSupplierId) {
                    el.val(this.selectedSupplierId).trigger('change.select2');
                }

                el.on('change', (e) => {
                    this.selectedSupplierId = e.target.value ? String(e.target.value) : '';
                    this.fetchMprItems();
                });

                // Release hydration flag after Select2 bindings are complete
                this.$nextTick(() => { this.hydrating = false; });
            },

            fetchMprItems() {
                // Skip AJAX call when page loads in Edit mode so populated items aren't lost
                if (this.hydrating || !this.selectedStyleId || !this.selectedSupplierId) return;

                this.loading = true;
                let url = "{{ route('tenant.api.get-mpr-items', ['style_id' => '__sid', 'supplier_id' => '__supid']) }}"
                          .replace('__sid', this.selectedStyleId).replace('__supid', this.selectedSupplierId);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => { this.items = data; })
                    .catch(() => { this.items = []; })
                    .finally(() => { this.loading = false; });
            },

            removeItem(index) { this.items.splice(index, 1); },

            // Standard Billing Computations
            subtotal() {
                return this.items.reduce((ttl, item) => ttl + ((item.order_qty || 0) * (item.unit_price || 0)), 0);
            },
            additionalCosts() {
                return (this.transportCost || 0) + (this.loaderBill || 0) + (this.inspectionBill || 0) + (this.extraCharges || 0);
            },
            grandTotal() {
                return (this.subtotal() + this.additionalCosts()) - (this.discount || 0);
            },

            submitForm(status) {
                if (!this.selectedStyleId || !this.selectedSupplierId) {
                    alert("Please complete all required fields (*).");
                    return;
                }

                this.pendingStatus = status;
                this.isSaving = true;

                let url = this.isEdit
                    ? "{{ route('tenant.purchase.po.update', ['id' => '__poid']) }}".replace('__poid', this.poId)
                    : "{{ route('tenant.purchase.po.store') }}";

                let payload = {
                    style_id: this.selectedStyleId,
                    supplier_id: this.selectedSupplierId,
                    po_date: this.poDate,
                    delivery_date: this.deliveryDate,
                    subtotal: this.subtotal(),
                    transport_cost: this.transportCost,
                    loader_bill: this.loaderBill,
                    inspection_bill: this.inspectionBill,
                    extra_charges: this.extraCharges,
                    discount: this.discount,
                    grand_total: this.grandTotal(),
                    payment_terms_text: this.paymentTermsText,
                    remarks: this.remarks,
                    status: status,
                    items: this.items
                };

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.isEdit ? { ...payload, _method: 'PUT' } : payload)
                })
                .then(async res => {
                    let data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(data => {
                    this.isSaving = false;
                    if (data.success) {
                        window.location.href = "{{ route('tenant.purchase.po.index') }}";
                    }
                })
                .catch(err => {
                    this.isSaving = false;
                    let msg = err.errors ? Object.values(err.errors).flat().join("\n") : (err.message || 'Operation failed');
                    alert("Error:\n" + msg);
                });
            }
        }
    }
</script>
@endsection