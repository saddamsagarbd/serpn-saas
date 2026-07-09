@extends('layouts.tenant')
@section('title','Sales Invoices & Orders')
@section('content')
<div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4">
    <!-- টপবার সেকশন -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
        <div>
            <h4 class="text-base font-bold text-slate-900">Sales Invoices & Orders</h4>
            <p class="text-xs text-slate-400 mt-0.5">Central repository containing point-of-sale transactions and distributed standard client orders.</p>
        </div>
        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition">
            + Create New Invoice
        </button>
    </div>

    <!-- ফিল্টার ও কোয়েরি কন্ট্রোল প্যানেল -->
    <div class="px-6 py-2 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-72">
            <input type="text" placeholder="Search by Invoice # or Customer..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-700 font-medium">
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <select class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-600 font-semibold">
                <option>All Payment Statuses</option>
                <option>Settled / Paid</option>
                <option>Partial Outstanding</option>
                <option>Unpaid</option>
            </select>
        </div>
    </div>

    <!-- সেলস ডেটা গ্রিড -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4 pl-6">Invoice ID</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Customer Details</th>
                    <th class="p-4">Payment Method</th>
                    <th class="p-4 text-right">Grand Total</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 pr-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                <!-- Row 1 -->
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-bold font-mono text-indigo-600 tracking-wide">INV-2026-0782</td>
                    <td class="p-4 font-mono text-slate-500">09-07-2026</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Anisur Rahman</p>
                        <p class="text-[10px] text-slate-400">Phone: +880 171X-XXXXXX</p>
                    </td>
                    <td class="p-4 font-semibold text-slate-600">Mobile Financial (Bkash)</td>
                    <td class="p-4 text-right font-bold font-mono text-slate-900">21,500 ৳</td>
                    <td class="p-4 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Paid</span>
                    </td>
                    <td class="p-4 pr-6 text-right space-x-1">
                        <button type="button" class="text-slate-500 hover:text-indigo-600 bg-slate-50 p-1.5 rounded-lg border border-slate-200/60 transition">👁️ View</button>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-bold font-mono text-indigo-600 tracking-wide">INV-2026-0781</td>
                    <td class="p-4 font-mono text-slate-500">08-07-2026</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">Beximco Office Corp (Bulk Account)</p>
                        <p class="text-[10px] text-slate-400">POS Counter Terminal-1</p>
                    </td>
                    <td class="p-4 font-semibold text-slate-600">Bank Transfer / Check</td>
                    <td class="p-4 text-right font-bold font-mono text-slate-900">145,000 ৳</td>
                    <td class="p-4 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">Partial</span>
                    </td>
                    <td class="p-4 pr-6 text-right space-x-1">
                        <button type="button" class="text-slate-500 hover:text-indigo-600 bg-slate-50 p-1.5 rounded-lg border border-slate-200/60 transition">👁️ View</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ফুটার পেজিনেশন -->
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/20">
        <span class="text-xs text-slate-400 font-medium">Showing 1 to 2 of 140 invoices</span>
        <div class="flex items-center gap-2">
            <button type="button" class="px-3 py-1 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Previous</button>
            <button type="button" class="px-3 py-1 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Next</button>
        </div>
    </div>
</div>
@endsection