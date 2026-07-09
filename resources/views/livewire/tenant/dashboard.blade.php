<div class="space-y-8">
    
    <!-- হেডার গ্রিটিংস এবং সাবস্ক্রিপশন স্ট্যাটাস -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-gradient-to-r from-indigo-900 to-slate-900 p-6 rounded-2xl shadow-sm text-white">
        <div>
            <h1 class="text-2xl font-black tracking-tight">Welcome Back, Enterprise Tenant 👋</h1>
            <p class="text-xs text-indigo-200 mt-1">Here is a quick overview of your workspace performance and consumption for today.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/20">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></div>
            <div class="text-xs">
                <p class="font-bold">Enterprise Plan</p>
                <p class="text-[10px] text-indigo-200">Renews on: <span class="font-mono">Dec 31, 2026</span></p>
            </div>
        </div>
    </div>

    <!-- ১. স্ট্যাটিস্টিক্যাল সেকশন: মূল ৪টি কেপিআই কার্ড -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- কার্ড ১: মান্থলি রিকারিং রেভিনিউ (MRR) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Monthly Revenue</p>
                <h3 class="text-3xl font-black text-slate-900 font-mono">৳৪,৮৫,০০০</h3>
                <span class="inline-flex items-center text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                    ↑ 14.2% from last month
                </span>
            </div>
            <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- কার্ড ২: একটিভ ইউজার বা স্টাফ সীট -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">User Licenses</p>
                <h3 class="text-3xl font-black text-slate-900 font-mono">38 <span class="text-xs font-normal text-slate-400">/ 50 Seats</span></h3>
                <div class="w-28 bg-slate-100 rounded-full h-1.5 mt-2">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: 76%"></div>
                </div>
            </div>
            <div class="p-3.5 bg-sky-50 text-sky-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        </div>

        <!-- কার্ড ৩: ডাটাবেজ বা এপিআই ইউসেজ কোটা -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">API Requests</p>
                <h3 class="text-3xl font-black text-slate-900 font-mono">84.2k <span class="text-xs font-normal text-slate-400">/ 100k</span></h3>
                <span class="inline-flex items-center text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                    ⚠️ 84% Quota Reached
                </span>
            </div>
            <div class="p-3.5 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        <!-- কার্ড ৪: সাকসেসফুল ট্রানজেকশন রেট -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Success Rate</p>
                <h3 class="text-3xl font-black text-slate-900 font-mono">99.98%</h3>
                <span class="inline-flex items-center text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                    ✓ All nodes healthy
                </span>
            </div>
            <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>

    <!-- ২. মেইন কন্টেন্ট গ্রিড: গ্রাফ চার্ট এবং রিসেন্ট ট্রানজেকশন টেবিল -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- বামদিকের বড় উইজেট: এনালাইটিক্স চার্ট মকআপ -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm lg:col-span-2 space-y-4">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Tenant Billing Analytics</h4>
                    <p class="text-xs text-slate-400">Overview of invoices generated across domains</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg">Weekly</button>
                    <button class="px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg">Monthly</button>
                </div>
            </div>
            
            <!-- বার গ্রাফ বা চার্ট প্লেসহোল্ডার (স্ট্যাটিক ডিজাইন) -->
            <div class="h-64 bg-slate-50 rounded-xl flex items-end justify-between p-6 space-x-4 border border-dashed border-slate-200">
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-100 hover:bg-indigo-600 h-24 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Jan</span>
                </div>
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-100 hover:bg-indigo-600 h-40 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Feb</span>
                </div>
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-100 hover:bg-indigo-600 h-32 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Mar</span>
                </div>
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-100 hover:bg-indigo-600 h-52 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Apr</span>
                </div>
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-200 hover:bg-indigo-600 h-44 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">May</span>
                </div>
                <div class="w-full space-y-2 text-center">
                    <div class="bg-indigo-600 h-56 w-full rounded-t-lg transition-all duration-300"></div>
                    <span class="text-[10px] font-bold text-slate-700 font-black uppercase">Jun</span>
                </div>
            </div>
        </div>

        <!-- ডানদিকের ছোট উইজেট: রিসেন্ট সাব-ডোমেন স্ট্যাটাস লকিং -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <div>
                <h4 class="text-base font-bold text-slate-900">Connected Nodes</h4>
                <p class="text-xs text-slate-400">Active tenant application clusters</p>
            </div>
            
            <div class="space-y-3">
                <!-- নোড ১ -->
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-800">house57.serpn-saas.test</p>
                        <p class="text-[10px] text-slate-400 font-mono">DB Isolation: Tenant_57</p>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full">Healthy</span>
                </div>
                <!-- নোড ২ -->
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-800">apex.serpn-saas.test</p>
                        <p class="text-[10px] text-slate-400 font-mono">DB Isolation: Tenant_89</p>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full">Healthy</span>
                </div>
                <!-- নোড ৩ -->
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-800">nexus.serpn-saas.test</p>
                        <p class="text-[10px] text-slate-400 font-mono">DB Isolation: Tenant_12</p>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full">Syncing</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ৩. নিচের ফুল-উইডথ উইজেট: রিসেন্ট ট্রানজেকশন/অর্ডার লগ টেবিল -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h4 class="text-base font-bold text-slate-900">Recent Purchase Orders & Billings</h4>
                <p class="text-xs text-slate-400">Live feed of global workspace logs and order generation</p>
            </div>
            <button class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                View All Logs
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="p-4 pl-6">Order ID</th>
                        <th class="p-4">Customer / Vendor</th>
                        <th class="p-4">Date Added</th>
                        <th class="p-4 text-right">Amount</th>
                        <th class="p-4 text-center">Gateway</th>
                        <th class="p-4 pr-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    <!-- রো ১ -->
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 pl-6 font-bold font-mono text-indigo-600">PO-20260707001</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-900">Apex Logistics & Materials</p>
                            <p class="text-[10px] text-slate-400">procure@apex.com</p>
                        </td>
                        <td class="p-4 text-slate-500">08-07-2026</td>
                        <td class="p-4 text-right font-bold font-mono text-slate-900">৳৩,২৭,৭৫০.০০</td>
                        <td class="p-4 text-center font-semibold text-slate-500">bKash B2B</td>
                        <td class="p-4 pr-6 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Paid & Verified</span>
                        </td>
                    </tr>
                    <!-- রো ২ -->
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 pl-6 font-bold font-mono text-indigo-600">PO-20260707002</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-900">TechVerse Bangladesh</p>
                            <p class="text-[10px] text-slate-400">billing@techverse.com</p>
                        </td>
                        <td class="p-4 text-slate-500">07-07-2026</td>
                        <td class="p-4 text-right font-bold font-mono text-slate-900">৳১,৮৫,০০০.০০</td>
                        <td class="p-4 text-center font-semibold text-slate-500">Bank Wire</td>
                        <td class="p-4 pr-6 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Pending QA</span>
                        </td>
                    </tr>
                    <!-- রো ৩ -->
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 pl-6 font-bold font-mono text-indigo-600">PO-20260705018</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-900">Nexus Trading Global</p>
                            <p class="text-[10px] text-slate-400">accounts@nexus.com</p>
                        </td>
                        <td class="p-4 text-slate-500">05-07-2026</td>
                        <td class="p-4 text-right font-bold font-mono text-slate-900">৳৫,১২,০০০.০০</td>
                        <td class="p-4 text-center font-semibold text-slate-500">City Bank API</td>
                        <td class="p-4 pr-6 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Paid & Verified</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>