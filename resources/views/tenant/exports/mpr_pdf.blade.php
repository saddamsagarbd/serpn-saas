<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>MPR Order BOM - {{ $salesOrder->order_number ?? '' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 12px;
            font-weight: bold;
            color: #4338ca;
            margin-top: 3px;
        }

        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 10px;
        }

        /* BOM Table Styling */
        .bom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .bom-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
        }

        .bom-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; }

        .qty-badge {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td width="60%" style="vertical-align: top;">
                <div class="company-title">{{ tenant()->company_name ?? 'Company Name' }}</div>
                <div class="sub-title">MPR Order</div>
                <div style="font-size: 9px; color: #94a3b8; margin-top: 2px;">
                    Calculated BOM requirements grouped by SKU matrix line items.
                </div>
            </td>
            <td width="40%" class="text-right" style="vertical-align: top;">
                <table width="100%" cellpadding="2">
                    <tr>
                        <td class="meta-label" class="text-right">Total Order Qty:</td>
                        <td class="text-right">
                            <span class="qty-badge">{{ number_format($consolidatedMrpDetails['total_order_qty'] ?? 0) }} Pcs</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label" class="text-right">Export Date:</td>
                        <td class="text-right">{{ date('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Consolidated Order BOM Table -->
    <table class="bom-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">Sl#</th>
                <th width="10%" class="text-center">Category</th>
                <th width="20%" class="text-center">Product Details</th>
                <th width="15%" class="text-center">Color</th>
                <th width="12%" class="text-center">Size</th>
                <th width="13%" class="text-center">Cons/GMT</th>
                <th width="8%" class="text-center">Unit</th>
                <th width="17%" class="text-right">Total Req Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consolidatedMrpDetails['bom_items'] as $index => $item)
                <tr>
                    <td class="font-bold text-center" style="color: #0f172a;">{{ ++$index }}</td>
                    <td class="font-bold text-left" style="color: #0f172a;">{{ $item['category'] }}</td>
                    <td class="font-bold text-left" style="color: #0f172a;">{{ $item['item_name'] }}</td>
                    <td class="text-center">{{ $item['color_name'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item['size_name'] ? ucfirst($item['size_name']) : 'N/A' }}</td>
                    <td class="text-center">{{ number_format($item['consumption'], 4) }}</td>
                    <td class="text-center">{{ $item['unit'] }}</td>
                    <td class="text-right font-bold" style="color: #4338ca;">
                        {{ number_format($item['required_qty'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #94a3b8; padding: 15px;">
                        No BOM items found for this order.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>