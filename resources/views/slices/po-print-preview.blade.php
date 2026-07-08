<div x-show="showPreviewModal" 
    class="fixed inset-0 z-50 overflow-y-auto print:absolute print:inset-0 print:overflow-visible print:bg-white" 
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm print:hidden" @click="showPreviewModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 print:p-0 print:block print:min-h-0">
        
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-gray-100 p-8 transform transition-all my-8 print:my-0 print:p-0 print:border-none print:shadow-none print:w-full"
            x-trap.noscroll="showPreviewModal">
            
            <div class="flex justify-between items-center bg-slate-50 p-4 rounded-xl mb-8 border border-slate-200 print:!hidden">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        ✓ Approved & Verified
                    </span>
                    <p class="text-xs text-slate-500">Live Preview for <span class="font-bold font-mono text-indigo-600" x-text="activePO"></span></p>
                </div>
                <div class="flex gap-2">
                    <button @click="showPreviewModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        Close
                    </button>
                    <button onclick="window.print()" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print / Save PDF
                    </button>
                </div>
            </div>

            <div class="space-y-8 text-slate-800">
                
                <div class="flex justify-between items-start border-b border-slate-200 pb-6">
                    <div>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Your Company Ltd.</h1>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 mt-1">Supply Chain & Procurement Dept.</p>
                        <div class="text-xs text-slate-500 mt-3 space-y-0.5 leading-relaxed">
                            <p>Corporate Office: House 12, Road 4, Banani, Dhaka, Bangladesh</p>
                            <p>Phone: +880 2-XXXXXXX | Email: procure@company.com</p>
                            <p>BIN/Tax ID: 1122334455-001</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-2xl font-black uppercase tracking-wider text-slate-900">Purchase Order</h2>
                        <div class="mt-4 font-mono text-xs space-y-1 text-slate-600">
                            <p><span class="text-slate-400">PO Number:</span> <span class="font-bold text-slate-900 text-sm" x-text="activePO"></span></p>
                            <p><span class="text-slate-400">PO Date:</span> 08-07-2026</p>
                            <p><span class="text-slate-400">Payment Terms:</span> Net 30 Days</p>
                            <p><span class="text-slate-400">Currency:</span> BDT (৳)</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 bg-slate-50 p-5 rounded-xl border border-slate-100 print:bg-transparent print:border-none print:p-0 print:grid print:grid-cols-2">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Vendor / Supplier</h3>
                        <div class="text-sm space-y-1">
                            <p class="font-bold text-slate-950">Apex Logistics & Materials Ltd.</p>
                            <p class="text-slate-600 font-medium">Attn: Md. Rahat Khan (Sales Manager)</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Plot 45, Sector 7, Uttara Commercial Area,<br>Dhaka-1230, Bangladesh</p>
                            <p class="text-xs font-mono text-slate-500 pt-1">Email: sales@apexlogistics.com | Tel: +880 1711-XXXXXX</p>
                        </div>
                    </div>
                    <div class="border-l border-slate-200 pl-8 print:pl-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Ship To / Delivery Destination</h3>
                        <div class="text-sm space-y-1">
                            <p class="font-bold text-slate-950">Central Warehousing Unit-2</p>
                            <p class="text-slate-600 font-medium">Attn: Delivery Gate Entry-3</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Plot 89, Industrial Zone, Gazipur Outpost,<br>Gazipur, Bangladesh</p>
                            <p class="text-xs font-semibold text-amber-700 pt-1">Expected Delivery Date: On or before 20-07-2026</p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden print:border-slate-300">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider print:bg-slate-50">
                                <th class="p-3 w-12 text-center">SL</th>
                                <th class="p-3">Item SKU & Description</th>
                                <th class="p-3 text-center w-24">Qty</th>
                                <th class="p-3 text-right w-32">Unit Price</th>
                                <th class="p-3 text-right w-36">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-200 text-slate-700">
                            <tr>
                                <td class="p-3.5 text-center font-mono text-slate-400">01</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">ITM-HW-04918 — Executive Ergonomic Mesh Chair</div>
                                    <div class="text-slate-400 mt-0.5">High-back, adjustable lumbar support, 3D armrest (Color: Black)</div>
                                </td>
                                <td class="p-3.5 text-center font-semibold">12 Pcs</td>
                                <td class="p-3.5 text-right font-mono">12,500.00</td>
                                <td class="p-3.5 text-right font-bold font-mono text-slate-900">1,500,00.00</td>
                            </tr>
                            <tr>
                                <td class="p-3.5 text-center font-mono text-slate-400">02</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">ITM-EL-11029 — Premium USB-C Multi-Port Hub</div>
                                    <div class="text-slate-400 mt-0.5">8-in-1 Dual HDMI 4K output, 100W PD charging, Aluminum alloy body</div>
                                </td>
                                <td class="p-3.5 text-center font-semibold">30 Pcs</td>
                                <td class="p-3.5 text-right font-mono">4,500.00</td>
                                <td class="p-3.5 text-right font-bold font-mono text-slate-900">1,35,000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-start pt-2 print:flex print:justify-between">
                    <div class="max-w-md text-[11px] text-slate-400 space-y-1 bg-slate-50 p-3 rounded-lg border border-slate-100 print:bg-transparent print:border-none print:p-0">
                        <p class="font-bold uppercase text-slate-500 tracking-wide">Terms & Instructions:</p>
                        <p>1. Please reference the PO number above on all shipping documents and invoices.</p>
                        <p>2. Goods arriving damaged or failing QA standard inspection will be returned at vendor's cost.</p>
                        <p>3. Submit invoices immediately upon delivery completion to accounts@company.com.</p>
                    </div>

                    <div class="w-72 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal:</span>
                            <span class="font-mono font-medium text-slate-800">2,85,000.00</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>VAT / Tax (15%):</span>
                            <span class="font-mono font-medium text-slate-800">42,750.00</span>
                        </div>
                        <div class="flex justify-between text-slate-500 pb-2 border-b border-slate-200">
                            <span>Shipping & Handling:</span>
                            <span class="font-mono font-medium text-slate-800">0.00</span>
                        </div>
                        <div class="flex justify-between text-slate-900 font-black text-sm pt-1">
                            <span>Grand Total (BDT):</span>
                            <span class="font-mono tracking-tight text-base">3,27,750.00</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-16 pt-16 mt-16 text-center print:grid print:grid-cols-2">
                    <div class="space-y-2">
                        <div class="mx-auto w-44 border-b border-slate-300 h-6"></div>
                        <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Prepared & Issued By</p>
                        <p class="text-[10px] text-slate-400 font-mono">Procurement Operations Officer</p>
                    </div>
                    <div class="space-y-2">
                        <div class="mx-auto w-44 border-b-2 border-slate-800 h-6 flex justify-center items-center">
                            <span class="text-[10px] font-mono font-black text-indigo-600 rotate-[-3deg] tracking-wider border border-dashed border-indigo-400 px-2 py-0.5 bg-indigo-50/50 rounded uppercase print:inline-block">
                                SYSTEM VERIFIED
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Authorized Management</p>
                        <p class="text-[10px] text-slate-400 font-mono">Digitally Approved via Secure Workflow</p>
                    </div>
                </div>

            </div>
            </div>
    </div>
</div>