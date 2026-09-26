<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production BOM & Cost Sheet</title>
    <style>
        @page {
            margin: 25px 30px 40px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        /* Header section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
            text-align: right;
            text-transform: uppercase;
        }
        /* Meta Information Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .meta-table td {
            padding: 6px 10px;
            width: 25%;
            vertical-align: top;
        }
        .meta-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            display: block;
        }
        .meta-value {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        /* Item Tables */
        .section-header {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            background-color: #e0e7ff;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 5px;
            border-left: 4px solid #4f46e5;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 5px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Summary & Totals */
        .grand-total-box {
            width: 40%;
            float: right;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .grand-total-box td {
            padding: 6px 10px;
            border: 1px solid #0f172a;
        }
        /* Footer & Page Numbers */
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-number:before {
            content: "Page " counter(page);
        }
        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td>
                <div class="company-title">{{ tenant('company_name') ?? 'GARMENT MANUFACTURING ERP' }}</div>
                <div style="font-size: 10px; color: #64748b;">Production Bill of Materials (BOM) & Cost Sheet</div>
            </td>
            <td class="doc-title">
                {{ $bom->bom_number ?? 'BOM-'.str_pad($bom->id, 5, '0', STR_PAD_LEFT) }}
            </td>
        </tr>
    </table>

    <!-- Order Meta Info -->
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Style Code</span>
                <span class="meta-value">{{ $bom->salesOrder->style->code ?? $bom->salesOrder->style->style_number ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="meta-label">Style Name</span>
                <span class="meta-value">{{ $bom->salesOrder->style->product_name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="meta-label">Buyer</span>
                <span class="meta-value">{{ $bom->salesOrder->buyer->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="meta-label">MPR / Order No</span>
                <span class="meta-value">{{ $bom->salesOrder->buyer_po_number ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Order Quantity</span>
                <span class="meta-value">{{ number_format($bom->salesOrder->total_quantity ?? 0) }} Pcs</span>
            </td>
            <td>
                <span class="meta-label">Season</span>
                <span class="meta-value">{{ $bom->salesOrder->style->season->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="meta-label">Created Date</span>
                <span class="meta-value">{{ optional($bom->created_at)->format('d M, Y') }}</span>
            </td>
            <td>
                <span class="meta-label">Status</span>
                <span class="meta-value" style="color: #059669;">APPROVED</span>
            </td>
        </tr>
    </table>

    <!-- 1. Raw Materials Breakdown -->
    @php
        $materials = $bom->items->where('cost_type', 'Material');
        $processings = $bom->items->where('cost_type', 'Processing');
    @endphp

    <div class="section-header">1. Raw Materials & Trims Breakdown</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 30%;">Item Description</th>
                <th style="width: 12%;">Matrix Target</th>
                <th class="text-right" style="width: 10%;">Garment Qty</th>
                <th class="text-right" style="width: 9%;">Consumption</th>
                <th class="text-right" style="width: 10%;">Req. Qty</th>
                <th class="text-right" style="width: 10%;">Unit Price ($)</th>
                <th class="text-right" style="width: 12%;">Total Cost ($)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materials as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->category_name ?? $item->cost_head ?? 'N/A' }}</td>
                    <td><span class="font-bold">{{ $item->item_name }}</span></td>
                    <td class="text-center">{{ $item->matrix_target ?? 'ALL' }}</td>
                    <td class="text-right">{{ number_format($item->garment_qty) }}</td>
                    <td class="text-right">{{ number_format($item->consumption, 4) }}</td>
                    <td class="text-right font-bold">{{ number_format($item->req_qty, 2) }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 4) }}</td>
                    <td class="text-right font-bold">${{ number_format($item->total_cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #94a3b8;">No Material Items Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. Processing Services Breakdown -->
    @if($processings->count() > 0)
        <div class="section-header">2. Processing Services & Overhead Costs</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 20%;">Service Head</th>
                    <th style="width: 35%;">Service Description</th>
                    <th class="text-right" style="width: 12%;">Garment Qty</th>
                    <th class="text-right" style="width: 14%;">Rate / Unit ($)</th>
                    <th class="text-right" style="width: 15%;">Total Cost ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processings as $index => $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><span class="font-bold">{{ $item->cost_head ?? 'Processing Service' }}</span></td>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-right">{{ number_format($item->garment_qty) }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 4) }}</td>
                        <td class="text-right font-bold">${{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Grand Total Box -->
    <table class="grand-total-box">
        <tr>
            <td style="background-color: #f8fafc; font-weight: bold; text-transform: uppercase;">Material Total:</td>
            <td class="text-right font-bold">${{ number_format($materials->sum('total_cost'), 2) }}</td>
        </tr>
        @if($processings->count() > 0)
        <tr>
            <td style="background-color: #f8fafc; font-weight: bold; text-transform: uppercase;">Processing Total:</td>
            <td class="text-right font-bold">${{ number_format($processings->sum('total_cost'), 2) }}</td>
        </tr>
        @endif
        <tr style="background-color: #0f172a; color: #ffffff;">
            <td style="font-weight: bold; font-size: 11px; text-transform: uppercase;">Grand Total Budget:</td>
            <td class="text-right font-bold" style="font-size: 12px;">${{ number_format($bom->grand_total, 2) }}</td>
        </tr>
    </table>

    <div class="clear"></div>

    <!-- Signatures -->
    <table style="width: 100%; margin-top: 50px; border-collapse: collapse;">
        <tr>
            <td style="width: 33%; text-align: center;">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: bold;">Prepared By</div>
            </td>
            <td style="width: 33%; text-align: center;">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: bold;">Merchandising Manager</div>
            </td>
            <td style="width: 33%; text-align: center;">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: bold;">Authorized Signature</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <footer>
        <table style="width: 100%;">
            <tr>
                <td>Generated on {{ date('d M, Y h:i A') }}</td>
                <td class="text-right page-number"></td>
            </tr>
        </table>
    </footer>

</body>
</html>