@extends('layouts.tenant')
@section('title','Warehouse')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'purchase-order', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Warehouse List</h2>
                <a href="#" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Warehouse
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" placeholder="Search suppliers..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Code</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Address</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">WH-101</td>
                            <td class="p-4 font-semibold text-gray-900">Main Warehouse</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Uttara, Dhaka</td>
                            <td class="p-4 text-center space-x-1">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                <button class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">WH-102</td>
                            <td class="p-4 font-semibold text-gray-900">Sub Station</td>
                            <td class="p-4 font-mono text-xs text-gray-500">Airport, Dhaka</td>
                            <td class="p-4 text-center space-x-1">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                <button class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection