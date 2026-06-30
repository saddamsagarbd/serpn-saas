<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - {{ tenant('id') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-indigo-700 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold uppercase">🏪 {{ tenant('id') }} POS System</h1>
        <span class="bg-green-500 text-xs px-3 py-1 rounded-full font-semibold">Subscription: Premium Active</span>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-gray-500 text-sm font-medium uppercase">মোট বিক্রি (Today)</p>
                <p class="text-2xl font-bold text-gray-800">৳ ৪৫,৫০০</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm font-medium uppercase">কারেন্ট স্টক (Items)</p>
                <p class="text-2xl font-bold text-gray-800">১,২৫০ টি</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-gray-500 text-sm font-medium uppercase">আজকের নিট লাভ</p>
                <p class="text-2xl font-bold text-gray-800 text-green-600">+ ৳ ৮,৫০০</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
                <p class="text-gray-500 text-sm font-medium uppercase">মোট বাকি (Due)</p>
                <p class="text-2xl font-bold text-gray-800 text-red-600">৳ ৩,২০০</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">⚡ Quick Retail Sale (POS Screen)</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">কাস্টমারের নাম</label>
                            <input type="text" value="Rahat Khan" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">মোবাইল নাম্বার</label>
                            <input type="text" value="01712345678" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">বারকোড স্ক্যান / প্রোডাক্ট খুঁজুন</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Scan Barcode or Type Product Name..." value="Premium Basmati Rice 5kg [BC-9921]" class="w-full border border-gray-300 rounded p-2 bg-gray-50">
                            <button class="bg-indigo-600 text-white px-4 rounded font-medium hover:bg-indigo-700">Add</button>
                        </div>
                    </div>

                    <table class="w-full text-left border-collapse mt-4">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                                <th class="p-2">প্রোডাক্ট</th>
                                <th class="p-2">পরিমাণ</th>
                                <th class="p-2">মূল্য</th>
                                <th class="p-2">মোট</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <tr class="border-b">
                                <td class="p-2 font-medium">Basmati Rice 5kg</td>
                                <td class="p-2">২ টি</td>
                                <td class="p-2">৳৬৫০</td>
                                <td class="p-2 font-bold">৳১,৩০০</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-6 flex justify-between items-center bg-indigo-50 p-4 rounded">
                        <span class="text-lg font-bold text-indigo-900">Total Payable: ৳১,৩০০</span>
                        <a href="/invoice-demo" target="_blank" class="bg-green-600 text-white px-6 py-2 rounded font-bold hover:bg-green-700 shadow">
                            Generate & Print Invoice 🖨️
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">⚠️ Stock Alert (Warning)</h2>
                    <ul class="space-y-3 text-sm">
                        <li class="flex justify-between bg-red-50 p-2 rounded">
                            <span class="font-medium text-gray-700">Soyabean Oil 1L</span>
                            <span class="text-red-600 font-bold">Only 3 left</span>
                        </li>
                        <li class="flex justify-between bg-yellow-50 p-2 rounded">
                            <span class="font-medium text-gray-700">Miniket Rice 25kg</span>
                            <span class="text-yellow-700 font-bold">5 bags left</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>