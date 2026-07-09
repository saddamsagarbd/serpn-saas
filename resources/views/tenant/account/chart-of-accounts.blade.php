@extends('layouts.tenant')
@section('title','Chart of Accounts (COA)')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" x-data="{ currentTab: 'assets' }">
    
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-3">
        <div>
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Accounting Structure</h4>
            <p class="text-xs text-slate-400 mt-0.5">Core account classification trees.</p>
        </div>
        <div class="flex flex-col gap-1 pt-2">
            <button @click="currentTab = 'assets'" :class="currentTab === 'assets' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 1000 - Assets Accounts</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'liabilities'" :class="currentTab === 'liabilities' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 2000 - Liabilities</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'equity'" :class="currentTab === 'equity' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 3000 - Owner's Equity</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'income'" :class="currentTab === 'income' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 4000 - Operating Income</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
            <button @click="currentTab = 'expense'" :class="currentTab === 'expense' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-2.5 text-xs rounded-xl transition flex justify-between items-center">
                <span>📁 5000 - Operating Expenses</span>
                <span class="bg-white px-1.5 py-0.5 text-[10px] rounded border font-mono font-bold text-slate-500">Active</span>
            </button>
        </div>
    </div>

    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h4 class="text-base font-bold text-slate-900" x-text="currentTab.toUpperCase() + ' Accounts Tree'"></h4>
                <p class="text-xs text-slate-400 mt-0.5">Sub-ledgers assigned under primary classification context block.</p>
            </div>
            <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                + Create New Ledger Account
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-3">Ledger Code</th>
                        <th class="p-3 pb-3">Account Label Details</th>
                        <th class="p-3 pb-3 text-right">Current Ledger Balance</th>
                        <th class="p-3 pr-0 pb-3 text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody x-show="currentTab === 'assets'" class="text-xs divide-y divide-slate-100 text-slate-700">
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="p-3 pl-0 font-bold font-mono text-indigo-600">1010-001</td>
                        <td class="p-3">
                            <p class="font-bold text-slate-800">Petty Cash Operational Counter</p>
                            <p class="text-[10px] text-slate-400 font-medium">Type: Cash / Current Asset</p>
                        </td>
                        <td class="p-3 text-right font-black font-mono text-slate-900">45,000 ৳</td>
                        <td class="p-3 pr-0 text-center text-slate-400 hover:text-indigo-600 cursor-pointer">🛠️ Edit</td>
                    </tr>
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="p-3 pl-0 font-bold font-mono text-indigo-600">1020-005</td>
                        <td class="p-3">
                            <p class="font-bold text-slate-800">Dutch-Bangla Bank Corporate A/C</p>
                            <p class="text-[10px] text-slate-400 font-medium">Type: Bank Ledger Account</p>
                        </td>
                        <td class="p-3 text-right font-black font-mono text-slate-900">1,240,500 ৳</td>
                        <td class="p-3 pr-0 text-center text-slate-400 hover:text-indigo-600 cursor-pointer">🛠️ Edit</td>
                    </tr>
                </tbody>

                <tbody x-show="currentTab === 'income'" class="text-xs divide-y divide-slate-100 text-slate-700">
                    <tr class="hover:bg-slate-50/40 transition">
                        <td class="p-3 pl-0 font-bold font-mono text-indigo-600">4010-001</td>
                        <td class="p-3">
                            <p class="font-bold text-slate-800">Retail ERP POS Sales Income</p>
                            <p class="text-[10px] text-slate-400 font-medium">Type: Operating Revenue</p>
                        </td>
                        <td class="p-3 text-right font-black font-mono text-slate-900">8,450,000 ৳</td>
                        <td class="p-3 pr-0 text-center text-slate-400 hover:text-indigo-600 cursor-pointer">🛠️ Edit</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection