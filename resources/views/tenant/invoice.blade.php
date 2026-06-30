<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Mockup</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-200 p-8 flex justify-center">
    <div class="bg-white w-[400px] p-6 shadow-lg border border-gray-300 font-mono text-sm">
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold uppercase">{{ tenant('id') }} RETAIL</h2>
            <p class="text-xs text-gray-600">Mirpur, Dhaka | Tel: 01712345678</p>
            <p class="text-xs border-b border-dashed py-2">CASH MEMO / INVOICE</p>
        </div>
        <div class="mb-4 text-xs space-y-1">
            <p>Inv No: #INV-2026-001</p>
            <p>Date: {{ date('d-m-Y H:i') }}</p>
            <p>Customer: Rahat Khan (01712***678)</p>
        </div>
        <table class="w-full text-left border-b border-dashed mb-4">
            <thead>
                <tr class="border-b border-dashed text-xs">
                    <th class="pb-1">Item</th>
                    <th class="pb-1 text-center">Qty</th>
                    <th class="pb-1 text-right">Price</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                <tr>
                    <td class="py-1">Basmati Rice 5kg</td>
                    <td class="py-1 text-center">2</td>
                    <td class="py-1 text-right">1300.00</td>
                </tr>
            </tbody>
        </table>
        <div class="text-right space-y-1 font-bold">
            <p>Sub Total: ৳1300.00</p>
            <p class="text-green-700">Paid: ৳1300.00</p>
            <p>Due: ৳0.00</p>
        </div>
        <div class="text-center mt-6 border-t border-dashed pt-4">
            <p class="text-xs text-gray-600">Thank You For Shopping With Us!</p>
            <p class="text-[10px] text-gray-400 mt-2">Powered by SERPN SaaS</p>
        </div>
    </div>
</body>
</html>