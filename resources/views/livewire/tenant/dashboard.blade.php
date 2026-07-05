<div>
    <!-- 📊 স্ট্যাটিস্টিকস কার্ড গ্রিড -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- কার্ড ১: আজকের বিক্রি -->
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Today's Sales</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">৳ {{ number_format($todaySales, 2) }}</div>
        </div>

        <!-- কার্ড ২: এই মাসের খরচ -->
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
            <div class="text-sm font-medium text-gray-500 uppercase">This Month Expenses</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">৳ {{ number_format($monthExpenses, 2) }}</div>
        </div>

        <!-- কার্ড ৩: মোট বকেয়া -->
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Total Customer Dues</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">৳ {{ number_format($totalDues, 2) }}</div>
        </div>

        <!-- কার্ড ৪: মোট প্রোডাক্ট স্টক -->
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Total Items in Stock</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalProducts }} Pcs</div>
        </div>

    </div>

    <!-- 📑 রিসেন্ট ট্রানজেকশন টেবিল -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Invoices / Sales</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentSales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $sale->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->customer_name ?? 'Walking Customer' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No recent transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
