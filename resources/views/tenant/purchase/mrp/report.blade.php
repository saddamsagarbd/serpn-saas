@extends('layouts.tenant')
@section('title', 'MRP Order Report')
@section('content')

<div class="space-y-6">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">MRP Order Details</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Central hub for buying house style specification sheets.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Registered Garment Profiles</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchOrders()" placeholder="Search style code or name..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4">Material Description</th>
                            <th class="p-4">required qty</th>
                            <th class="p-4">In Hand</th>
                            <th class="p-4">Availability</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100">
                        @forelse($bomItems as $bomItem)
                            <tr>
                                <td class="p-4 font-medium text-gray-900">{{ $bomItem->item_description ?? $bomItem->item_name }}</td>
                                <td class="p-4 font-medium text-gray-900">{{ $bomItem->required_qty ?? 0 }}</td>
                                <td class="p-4 font-medium text-gray-900">{{ $bomItem->available_stock ?? $bomItem->in_hand ?? 0 }}</td>
                                <td class="p-4 font-medium text-gray-900">
                                    {{ ($bomItem->available_stock ?? 0) - ($bomItem->required_qty ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-400">No matching mrp order found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection