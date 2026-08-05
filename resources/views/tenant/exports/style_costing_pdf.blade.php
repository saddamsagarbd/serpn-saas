<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Style BOM Profile</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 12px; line-height: 1.4; }
        .header-title { text-align: center; font-size: 18px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; color: #1e293b; }
        .meta-table { w-full; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .meta-label { font-weight: bold; color: #64748b; font-size: 11px; }
        .bom-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .bom-table th { background-color: #0f172a; color: #ffffff; text-transform: uppercase; font-size: 10px; font-weight: bold; padding: 8px; text-align: left; }
        .bom-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #f8fafc; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header-title">{{ tenant()->company_name }} - {{ $style->style_number }} (Costing)</div>
    <div style="text-align: center; font-size: 9px; color: #94a3b8; margin-bottom: 20px;">Isolated Context: Tenant Profile Database</div>

    <!-- Metadata Grid Block -->
    <table width="100%" class="meta-table" cellspacing="5">
        <tr>
            <td class="meta-label" width="18%">Style Number:</td>
            <td width="32%">{{ $style->style_number }}</td>
            <td class="meta-label" width="18%">Buyer:</td>
            <td width="32%">{{ $style->buyer->name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Product Name:</td>
            <td>{{ $style->product_name }}</td>
            <td class="meta-label">Season Name:</td>
            <td>{{ $style->season->name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Target FOB:</td>
            <td style="font-weight: bold; color: #4f46e5;">${{ number_format($style->costing->target_fob, 2) }}</td>
            <td class="meta-label">Export Date:</td>
            <td>{{ date('Y-m-d H:i') }}</td>
        </tr>
    </table>

    <!-- BOM Grid Details Matrix -->
    <table class="bom-table">
        <thead>
            <tr>
                <th width="45%">Material Description</th>
                <th width="15%" class="text-center">Category</th>
                <th width="15%" class="text-center">Color</th>
                <th width="15%" class="text-center">Size</th>
                <th width="15%" class="text-right">Consumption</th>
                <th width="12%" class="text-right">Unit Price</th>
                <th width="13%" class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($style->costing->bomItems as $item)
                <tr>
                    <td>style_costing_pdf.blade.php<b>{{ $item->item_description }}</b></td>
                    <td class="text-center" style="text-transform: uppercase; font-size: 9px;">{{ $item->category }}</td>
                    <td class="text-center">{{ $item->color?->name ?? "N/A" }}</td>
                    <td class="text-center">{{ $item->size?->short_name ?? "N/A" }}</td>
                    <td class="text-right">{{ number_format($item->consumption, 4) }} <span style="color:#64748b; font-size:9px;">{{ $item->unit }}</span></td>
                    <td class="text-right">${{ number_format($item->unit_price, 4) }}</td>
                    <td class="text-right" style="font-weight: bold;">${{ number_format($item->total_cost, 4) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right" style="padding: 10px; font-size: 10px; uppercase;">Calculated Raw Material Total:</td>
                <td class="text-right" style="color: #10b981; font-size: 12px;">${{ number_format($style->costing->total_rm_cost, 4) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>