@extends('layouts.tenant')
@section('title','Point of Sale (POS)')
@section('content')
<div x-data="{ 
    searchQuery: '',
    products: [
        { id: 1, name: 'Execution Ergonomic Mesh Chair', sku: 'ITM-HW-04918', price: 12500, stock: 45, category: 'Furniture', inlineQty: 1 },
        { id: 2, name: 'Premium USB-C Multi-Port Hub', sku: 'ITM-EL-11029', price: 4500, stock: 120, category: 'Electronics', inlineQty: 1 },
        { id: 3, name: 'Wireless Mechanical Keyboard', sku: 'ITM-EL-99011', price: 8500, stock: 14, category: 'Electronics', inlineQty: 1 },
        { id: 4, name: 'Dell UltraSharp 27 Inch Monitor', sku: 'ITM-EL-44023', price: 38500, stock: 8, category: 'Electronics', inlineQty: 1 }
    ],
    cart: [
        { id: 1, name: 'Execution Ergonomic Mesh Chair', price: 12500, qty: 1, sku: 'ITM-HW-04918' },
        { id: 2, name: 'Premium USB-C Multi-Port Hub', price: 4500, qty: 2, sku: 'ITM-EL-11029' }
    ],
    discount: 500,
    taxRate: 0.05,
    
    get filteredProducts() {
        if (this.searchQuery.trim() === '') return this.products;
        return this.products.filter(p => 
            p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
            p.sku.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
    },
    
    addToCart(product) {
        let existing = this.cart.find(item => item.id === product.id);
        if (existing) {
            existing.qty += product.inlineQty;
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                qty: product.inlineQty,
                sku: product.sku
            });
        }
        product.inlineQty = 1; // Reset inline counter after adding
    },
    
    get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0); },
    get tax() { return this.subtotal * this.taxRate; },
    get total() { return (this.subtotal + this.tax) - this.discount; }
}" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        <div>
            <h4 class="text-base font-bold text-slate-900">Point of Sale (POS) Terminal</h4>
            <p class="text-xs text-slate-400 mt-0.5">Search products and manage operational quantities directly before pushing to invoice.</p>
        </div>

        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 text-sm">🔍</span>
            <input type="text" 
                x-model="searchQuery"
                placeholder="Type SKU or Product Title to instant search..." 
                class="w-full pl-10 pr-4 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-medium text-slate-700 transition">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 pl-0 pb-4 w-5/12">Item Identity</th>
                        <th class="p-3 pb-4 w-2/12 text-center">Unit Price</th>
                        <th class="p-3 pb-4 w-2/12 text-center">Quantity</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100 text-slate-700">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="py-4 pl-0">
                                <span class="text-[10px] font-bold font-mono text-indigo-500 bg-indigo-50/40 px-1.5 py-0.5 rounded" x-text="product.sku"></span>
                                <p class="font-bold text-slate-800 mt-1.5" x-text="product.name"></p>
                                <span class="text-[10px] text-slate-400 block mt-0.5" x-text="'Stock: ' + product.stock + ' Pcs'"></span>
                            </td>
                            <td class="py-4 text-center font-bold font-mono text-slate-900" x-text="product.price.toLocaleString() + ' ৳'"></td>
                            
                            <td class="py-4 text-center">
                                <div class="inline-flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white mx-auto shadow-xs">
                                    <button type="button" @click="product.inlineQty > 1 ? product.inlineQty-- : null" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">-</button>
                                    <span class="px-2 text-xs font-bold font-mono text-slate-800 text-center w-6" x-text="product.inlineQty"></span>
                                    <button type="button" @click="product.inlineQty++" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">+</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Current Checkout</h5>
            <span class="px-2 py-0.5 bg-indigo-50 rounded text-[10px] font-bold text-indigo-600 font-mono">Invoice</span>
        </div>

        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Select Account</label>
            <select class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 font-semibold text-slate-700">
                <option>Walk-In Counter Customer</option>
                <option>Anisur Rahman (VIP Profile)</option>
            </select>
        </div>

        <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto pr-1">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="py-3 flex justify-between items-center gap-2">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-800 truncate max-w-[150px]" x-text="item.name"></p>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="item.qty + 'x • ' + item.price.toLocaleString() + ' ৳'"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold font-mono text-slate-900" x-text="(item.price * item.qty).toLocaleString() + ' ৳'"></span>
                        <button type="button" @click="cart.splice(index, 1)" class="text-slate-300 hover:text-rose-500 ml-1">✕</button>
                    </div>
                </div>
            </template>
        </div>

        <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs font-medium text-slate-500">
            <div class="flex justify-between">
                <span>Sub Total</span>
                <span class="font-mono font-bold text-slate-800" x-text="subtotal.toLocaleString() + ' ৳'"></span>
            </div>
            <div class="flex justify-between">
                <span>VAT / Tax (5%)</span>
                <span class="font-mono font-bold text-slate-800" x-text="tax.toLocaleString() + ' ৳'"></span>
            </div>
            <div class="flex justify-between items-center text-rose-600">
                <span>Discount</span>
                <input type="number" x-model.number="discount" class="w-20 px-2 py-0.5 text-right font-mono font-bold border border-slate-200 rounded-lg bg-white focus:outline-none focus:border-rose-400 text-slate-800">
            </div>
            
            <div class="flex justify-between items-center pt-3 border-t border-slate-200 text-sm font-black text-slate-900">
                <span>Total Bill</span>
                <span class="font-mono text-indigo-600 text-base" x-text="total.toLocaleString() + ' ৳'"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-2 pt-2">
            <button type="submit" :disabled="cart.length === 0" :class="cart.length === 0 ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'" class="w-full text-white font-bold py-2.5 rounded-xl shadow-sm transition text-xs text-center">
                ⚡ Process Bill & Print
            </button>
            <button type="button" @click="cart = []" class="w-full bg-white hover:bg-slate-50 text-slate-500 font-bold py-2 rounded-xl border border-slate-200 text-center text-[11px] transition">
                Clear Ticket
            </button>
        </div>
    </div>
</div>
@endsection