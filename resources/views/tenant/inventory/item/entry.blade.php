@extends('layouts.tenant')
@section('title','Item Entry')
@section('content')
<div class="space-y-6" x-data="itemCreationApp({{ 
    json_encode([
        'isEdit' => isset($item),
        'id' => isset($item) ? $item->id : null,
        'item_code' => isset($item) ? $item->code : '',
        'item_name' => isset($item) ? $item->name : '',
        'item_type' => isset($item) ? $item->item_type : '',
        'unit_id' => isset($item) ? $item->unit_id : '',
    ])
}})">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">Create New Master Item</h3>
                <p class="text-xs text-gray-500 mt-1">Register a new product or component into the global inventory database.</p>
            </div>
            
            <form  @submit.prevent="submitForm" class="space-y-6">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Row 1: Item Name & Auto Code --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Name *</label>
                        <input type="text" 
                        x-model="itemName"
                        required 
                        placeholder="e.g., Premium Denim Fabric / Cotton Sewing Thread" 
                        class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Code (Auto Generated)</label>
                        <input type="text" 
                        x-model="itemCode"
                        readonly 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed">
                    </div>
                </div>

                {{-- Row 2: Item Type, Category, Brand, Unit --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Type *</label>
                        <select x-model="itemType" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold">
                            <option value="">-- Select Type --</option>
                            <option value="fabric" {{ (old('item_type', $item->item_type ?? '') == 'fabric') ? 'selected' : '' }}>Fabric</option>
                            <option value="trims" {{ (old('item_type', $item->item_type ?? '') == 'trims') ? 'selected' : '' }}>Trims</option>
                            <option value="accessories" {{ (old('item_type', $item->item_type ?? '') == 'accessories') ? 'selected' : '' }}>Accessories</option>
                            <option value="chemical" {{ (old('item_type', $item->item_type ?? '') == 'chemical') ? 'selected' : '' }}>Chemical</option>
                            <option value="finished-goods" {{ (old('item_type', $item->item_type ?? '') == 'finished-goods') ? 'selected' : '' }}>Finished Goods</option>
                            <option value="other" {{ (old('item_type', $item->item_type ?? '') == 'other') ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>                    

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Unit of Measure (UOM) *</label>
                        <select x-model="unitId" required class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold">
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ (old('unit_id', $item->unit_id ?? '') == $unit->id) ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->short_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>                 

                {{-- Action Buttons --}}
                <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('tenant.inventory.items.index') }}" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                        <span x-text="isSaving ? 'Saving Master...' : 'Save Item Master'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function itemCreationApp(initialData) {
    console.log(initialData);
    return {
        isEdit: initialData.isEdit,
        itemId: initialData.id,
        itemCode: initialData.item_code,
        itemName: initialData.item_name,
        itemType: initialData.item_type,
        unitId: initialData.unit_id,
        isSaving: false,

        submitForm() {
            if (!this.itemType.trim() || !this.itemName.trim()) {
                alert("Please complete all required fields (*).");
                return;
            }

            this.isSaving = true;

            let url = '';

            if(this.isEdit){
                url = "{{ route('tenant.inventory.item.update', ['id' => '__id']) }}";
                url = url.replace('__id', this.itemId);
            } else {
                url = "{{ route('tenant.inventory.item.store') }}";
            }

            let payload = {
                item_code: this.itemCode,
                item_name: this.itemName,
                item_type: this.itemType,
                unit_id: this.unitId,
            };

            if (this.isEdit) {
                payload._method = 'PUT';
            }

            fetch(url, {
                method: this.isEdit?'PUT':'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
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
                if (!data) return; // Exit if an error was already handled above

                this.isSaving = false;
                if (data.success) {
                    if (typeof toastr !== 'undefined') toastr.success(data.message || "Item master data loaded perfectly.")
                    window.location.href = "{{ route('tenant.inventory.items.index') }}";

                } else {
                    alert("Execution Error: " + data.message);
                }
            })
            .catch(error => {
                this.isSaving = false;
                console.error(error);
                alert("A genuine network or transport layer error occurred.");
            })
        }
    }
}
</script>
@endpush