<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MPR Order Details - {{ $salesOrder->order_number ?? '' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 11px;
            font-weight: bold;
            color: #4338ca;
            margin-top: 2px;
        }

        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Order Header Info Grid Section */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            width: 25%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .info-label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            display: block;
        }

        .info-value {
            font-size: 10px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 2px;
            display: block;
        }

        /* Data Table Styling */
        .bom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .bom-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 8px;
            font-weight: bold;
            padding: 6px;
            text-align: left;
        }

        .bom-table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; }

        .qty-badge {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 3px 6px;
            border-radius: 3px;
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
                <div class="sub-title">MPR Order Details</div>
                <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">
                    Calculated requirements grouped by SKU matrix line items.
                </div>
            </td>
            <td width="40%" class="text-right" style="vertical-align: top;">
                <table width="100%" cellpadding="2">
                    <tr>
                        <td class="meta-label text-right">Total Order Qty:</td>
                        <td class="text-right">
                            <span class="qty-badge">{{ number_format($consolidatedMrpDetails['total_order_qty'] ?? $salesOrder->items->sum('quantity') ?? 0) }} Pcs</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label text-right">Export Date:</td>
                        <td class="text-right" style="font-size: 9px;">{{ date('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 1. Order Header Information -->
    <div class="section-title">1. Order Header Information</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="info-label">Style</span>
                <span class="info-value">{{ $salesOrder->style->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Buyer Name (Sold-to Party)</span>
                <span class="info-value">{{ $salesOrder->buyer->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Buyer PO Number</span>
                <span class="info-value">{{ $salesOrder->buyer_po_number ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Ship To Party</span>
                <span class="info-value">{{ $salesOrder->ship_to_party ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Sales Org</span>
                <span class="info-value">{{ $salesOrder->sales_org ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Distribution Channel</span>
                <span class="info-value">{{ $salesOrder->distribution_channel ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Job Mode</span>
                <span class="info-value">{{ $salesOrder->job_mode ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Division / Merchant Team</span>
                <span class="info-value">{{ $salesOrder->merchant_team ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">PO Received Date</span>
                <span class="info-value">{{ $salesOrder->po_received_date ? \Carbon\Carbon::parse($salesOrder->po_received_date)->format('m / d / Y') : 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Requested Delivery Date</span>
                <span class="info-value">{{ $salesOrder->requested_delivery_date ? \Carbon\Carbon::parse($salesOrder->requested_delivery_date)->format('m / d / Y') : 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Advance Receive Date</span>
                <span class="info-value">{{ $salesOrder->advance_receive_date ? \Carbon\Carbon::parse($salesOrder->advance_receive_date)->format('m / d / Y') : 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Plant (Factory)</span>
                <span class="info-value">{{ $salesOrder->plant ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Shipping Point</span>
                <span class="info-value">{{ $salesOrder->shipping_point ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="info-label">Currency</span>
                <span class="info-value">{{ $salesOrder->currency ?? 'USD ($)' }}</span>
            </td>
            <td colspan="2"></td>
        </tr>
    </table>

    <!-- 2. Line Items (Color & Size Matrix) -->
    <div class="section-title">2. Line Items (Color & Size Matrix)</div>
    <table class="bom-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">Sl#</th>
                <th width="25%">Generated SKU</th>
                <th width="20%">Color</th>
                <th width="15%">Size</th>
                <th width="15%" class="text-right">Ordered Qty</th>
                <th width="20%" class="text-right">Unit Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesOrder->items as $index => $item)
                <tr>
                    <td class="font-bold text-center" style="color: #0f172a;">{{ $index + 1 }}</td>
                    <td class="font-bold" style="color: #4338ca;">{{ $item->sku ?? "N/A" }}</td>
                    <td>{{ $item->colorContext->name ?? 'N/A' }}</td>
                    <td>{{ $item->sizeChart->name ?? 'N/A' }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($item->quantity) }}</td>
                    <td class="text-right font-mono">{{ number_format($item->unit_price, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>