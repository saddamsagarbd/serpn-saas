@extends('layouts.tenant')
@section('title','Supplier Master')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'purchase-order', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'purchase-order'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Supplier List</h2>
                <a href="{{ route('tenant.purchase.suppliers.form') }}" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add Supplier
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
                            <th class="p-4">ID</th>
                            <th class="p-4">Supplier Name</th>
                            <th class="p-4">Contact No</th>
                            <th class="p-4">Organization</th>
                            <th class="p-4">Address</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">SUP-2026001</td>
                            <td class="p-4 font-semibold text-gray-900">Rahat Khan</td>
                            <td class="p-4 font-mono text-xs text-gray-500">+880 1711-XXXXXX</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs px-2.5 py-1 rounded-full font-semibold">
                                    Apex Logistics Ltd.
                                </span>
                            </td>
                            <td class="p-4 text-xs text-gray-600">House 45, Road 12, Dhanmondi, Dhaka</td>
                            <td class="p-4 text-center space-x-1">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                <button class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">SUP-2026002</td>
                            <td class="p-4 font-semibold text-gray-900">Sarah Jenkins</td>
                            <td class="p-4 font-mono text-xs text-gray-500">+880 1822-XXXXXX</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs px-2.5 py-1 rounded-full font-semibold">
                                    Logitech Systems
                                </span>
                            </td>
                            <td class="p-4 text-xs text-gray-600">Agrabad C/A, Chittagong</td>
                            <td class="p-4 text-center space-x-1">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                <button class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">SUP-2026003</td>
                            <td class="p-4 font-semibold text-gray-900">M. Chowdhury</td>
                            <td class="p-4 font-mono text-xs text-gray-500">+880 1633-XXXXXX</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs px-2.5 py-1 rounded-full font-semibold">
                                    Chowdhury Traders
                                </span>
                            </td>
                            <td class="p-4 text-xs text-gray-600">Zindabazar, Sylhet</td>
                            <td class="p-4 text-center space-x-1">
                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                <button class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-indigo-600 font-mono">SUP-2026004</td>
                            <td class="p-4 font-semibold text-gray-900">Ahsan Habib</td>
                            <td class="p-4 font-mono text-xs text-gray-500">+880 1944-XXXXXX</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs px-2.5 py-1 rounded-full font-semibold">
                                    Global Textile Corp
                                </span>
                            </td>
                            <td class="p-4 text-xs text-gray-600">Uttara Sector 4, Dhaka</td>
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