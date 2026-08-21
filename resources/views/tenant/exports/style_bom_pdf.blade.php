<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>Buyer Cost Sheet - {{ $style->style_number }}</title>
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
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sheet-title {
            font-size: 12px;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 10px;
        }

        .product-img {
            max-width: 110px;
            max-height: 110px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        /* Cost Sheet Main Table */
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .cost-table th, .cost-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }

        .cost-table th {
            background-color: #d97706;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; }

        /* Section Specific Styling */
        .bg-ttl-section {
            background-color: #fef3c7;
            font-weight: bold;
            color: #78350f;
        }

        .bg-base-cost {
            background-color: #78350f;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
        }

        .bg-revenue {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
        }

        .bg-fob {
            background-color: #78350f;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
        }

        .bg-offered {
            background-color: #10b981;
            color: #022c22;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>

@php
    $costing = $style->costing;
    $bomItems = $costing ? $costing->bomItems : collect();
    
    // Grouping BOM items (Exact matching with Cost Sheet)
    $fabrics = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') === 'fabrics');
    $trims = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') !== 'fabrics');

    // Helper closure to calculate item total with Wastage %
    $calcItemTotal = function($item) {
        $cons = floatval($item->consumption ?? 0);
        $price = floatval($item->unit_price ?? 0);
        $wastage = floatval($item->wastage_percent ?? 0);
        return ($cons * $price) * (1 + ($wastage / 100));
    };

    // Totals calculation
    $ttlFabricCost = $fabrics->sum($calcItemTotal);
    $ttlTrimCost = $trims->sum($calcItemTotal);
    $grandTotalMaterialCost = $ttlFabricCost + $ttlTrimCost;

    $targetPrice = $costing->target_fob ?? 0;
    $currencySymbol = ($costing->currency ?? 'USD') === 'USD' ? '$' : '৳';
@endphp

    <!-- HEADER / META INFORMATION MATRIX -->
    <table class="header-table">
        <tr>
            <td width="70%" style="vertical-align: top;">
                <div class="company-name">{{ tenant()->company_name ?? 'Company Name' }}</div>
                <div class="sheet-title">Buyer Cost Sheet</div>
                
                <table width="100%" style="margin-top: 8px;" cellpadding="2">
                    <tr>
                        <td class="meta-label" width="22%">Style Number:</td>
                        <td width="28%"><b>{{ $style->style_number }}</b></td>
                        <td class="meta-label" width="20%">Buyer:</td>
                        <td width="30%">{{ $style->buyer->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Product Name:</td>
                        <td>{{ $style->product_name }}</td>
                        <td class="meta-label">Season:</td>
                        <td>{{ $style->season->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Target FOB:</td>
                        <td style="font-weight: bold; color: #4f46e5;">{{ $currencySymbol }} {{ number_format($costing->target_fob ?? 0, 2) }}</td>
                        <td class="meta-label">Export Date:</td>
                        <td>{{ date('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-right" style="vertical-align: top;">
                @php
                    $fullPath = storage_path('/app/public/' . $style->product_image);
                @endphp
                @if($style->product_image && file_exists($fullPath))
                    <img src="{{ $fullPath }}" class="product-img" alt="{{ $style->product_name }}">
                @endif
            </td>
        </tr>
    </table>

    <!-- MAIN COST SHEET TABLE -->
    <table class="cost-table">
        <thead>
            <tr>
                <th width="28%">Item</th>
                <th width="20%">Details</th>
                <th width="12%" class="text-right">Cons/Qnty</th>
                <th width="13%" class="text-right">Unit Price</th>
                <th width="12%" class="text-center">Wastage (%)</th>
                <th width="15%" class="text-right">TTL Cost</th>
            </tr>
        </thead>
        <tbody>

            <!-- 1. FABRIC ITEMS -->
            @forelse($fabrics as $item)
            <tr>
                <td class="font-bold" style="color: #312e81;">{{ $item->itemMaster->category->name ?? $item->item_description ?? 'Fabrics' }}</td>
                <td style="color: #475569;">
                    {{ $item->item->details ?? $item->item_description }}
                    @if(optional($item->item)->uom || $item->item_unit)
                        <span style="font-size: 8px; color: #64748b;">({{ $item->item_unit ?? $item->item->uom }})</span>
                    @endif
                </td>
                <td class="text-right font-mono">{{ number_format($item->consumption, 2) }}</td>
                <td class="text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right font-mono">{{ number_format($item->wastage_percent, 2) }}%</td>
                <td class="text-right font-mono font-bold">{{ $currencySymbol }} {{ number_format($item->consumption * $item->unit_price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="color: #94a3b8; font-style: italic;">No fabric items added.</td>
            </tr>
            @endforelse

            <!-- TOTAL FABRIC COST -->
            <tr class="bg-ttl-section">
                <td colspan="3">TTL FABRIC COST</td>
                <td class="text-right font-mono">{{ number_format($fabrics->sum('consumption'), 2) }}</td>
                <td></td>
                <td class="text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlFabricCost, 2) }}</td>
            </tr>

            <!-- 2. TRIMS & ACCESSORIES -->
            @foreach($trims as $item)
            <tr>
                <td class="font-bold" style="color: #334155;">{{ $item->item_description }}</td>
                <td style="color: #475569;">
                    {{ $item->item->details ?? $item->item_description }}
                    @if(optional($item->itemMaster)->unit || $item->item_unit)
                        <span style="font-size: 8px; color: #64748b;">({{ $item->item_unit ?? $item->itemMaster->unit->short_name }})</span>
                    @endif
                </td>
                <td class="text-right font-mono">{{ number_format($item->consumption, 2) }}</td>
                <td class="text-right font-mono">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right font-mono">{{ number_format($item->wastage_percent, 2) }}%</td>
                <td class="text-right font-mono font-bold">{{ $currencySymbol }} {{ number_format($item->consumption * $item->unit_price, 2) }}</td>
            </tr>
            @endforeach

            <!-- TOTAL TRIM COST -->
            <tr class="bg-ttl-section">
                <td colspan="5">TTL TRIM COST</td>
                <td class="text-right font-mono">{{ $currencySymbol }} {{ number_format($ttlTrimCost, 2) }}</td>
            </tr>

            <!-- GRAND TOTAL MATERIAL COST -->
            <tr class="bg-offered">
                <td colspan="5">TOTAL MATERIAL COST (FABRIC + TRIMS)</td>
                <td class="text-right font-mono" style="font-size: 14px;">{{ $currencySymbol }} {{ number_format($grandTotalMaterialCost, 2) }}</td>
            </tr>

        </tbody>
    </table>

</body>
</html>