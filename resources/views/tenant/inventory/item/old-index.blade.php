@extends('layouts.tenant')
@section('title','Item Master')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'items', openModal: false }">
    @if(session('success'))
        <div class="p-4 mb-4 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'items'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Item Master Registry</h2>
                <a href="{{ route('tenant.inventory.item.create') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Item Master
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Active</span>
                    <input type="text" placeholder="Search item registry..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">SKU Code</th>
                            <th class="p-4">Style No / Specification</th>
                            <th class="p-4">Item Name</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">UOM</th>
                            <th class="p-4 text-right">Live Stock</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-indigo-600 font-mono text-xs">{{ $item->sku }}</td>
                                <td class="p-4 font-mono text-xs text-gray-700">
                                    <span class="font-bold text-slate-900">{{ $item->style_no ?? 'N/A' }}</span>
                                    @if($item->fabric_code)
                                        <span class="block text-[10px] text-gray-400 font-sans">Code: {{ $item->fabric_code }}</span>
                                    @endif
                                </td>
                                <td class="p-4 font-semibold text-gray-900 text-xs">{{ $item->name }}</td>
                                <td class="p-4 text-xs font-medium text-gray-500">{{ $item->category->name }}</td>
                                <td class="p-4 text-xs font-mono text-gray-500">{{ $item->unit->short_name }}</td>
                                <td class="p-4 text-right font-mono font-bold text-xs {{ $item->stock_qty > 0 ? 'text-slate-900' : 'text-rose-500' }}">
                                    {{ number_format($item->stock_qty) }}
                                </td>
                                <td class="p-4 text-center space-x-1">
                                    <button class="bg-gray-100 text-gray-600 text-[11px] px-2.5 py-1 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                    <button class="bg-gray-100 text-red-600 text-[11px] px-2.5 py-1 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-xs text-gray-400 font-medium">
                                    No raw material items or products registered in global database master catalog.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection