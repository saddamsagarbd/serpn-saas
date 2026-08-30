<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>Production BOM - {{ $style->style_number }}</title>
    <style>
        @page {
            margin: 15px;
            size: A4 landscape; /* প্রোডাকশন BOM-এ বেশি কলাম থাকায় Landscape বেস্ট */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sheet-title {
            font-size: 11px;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .meta-label {
            font-weight: bold;
            color: #475569;
            font-size: 8px;
        }

        .product-img {
            max-width: 70px;
            max-height: 70px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }

        /* Production Table Styling */
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .cost-table th, .cost-table td {
            border: 1px solid #94a3b8;
            padding: 4px 5px;
        }

        .cost-table th {
            background-color: #1e293b;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 7.5px;
            font-weight: bold;
            text-align: left;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; }

        /* Section Specific Styling */
        .bg-section-header {
            background-color: #e2e8f0;
            font-weight: bold;
            color: #0f172a;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        .bg-ttl-section {
            background-color: #fef3c7;
            font-weight: bold;
            color: #78350f;
        }

        .bg-grand-total {
            background-color: #0f172a;
            color: #f59e0b;
            font-weight: bold;
            font-size: 10px;
        }

        .status-badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
        }
        .status-inhouse { background-color: #d1fae5; color: #065f46; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
        .status-pending { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

@php
    $costing = $style->costing;
    $bomItems = $costing ? $costing->bomItems : collect();
    
    // Grouping Fabrics & Trims
    $fabrics = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') === 'fabrics');
    $trims = $bomItems->filter(fn($i) => strtolower($i->category ?? $i->itemMaster->item_type ?? '') !== 'fabrics');


    // Controller focus: $orderQty parameter passed from MPR
    $orderQuantity = floatval($orderQty ?? 1);
    $currencySymbol = ($costing->currency ?? 'USD') === 'USD' ? '$' : '৳';

    // Calculation Helper Function
    $calcRowData = function($item) use ($orderQuantity) {
        $cons = floatval($item->consumption ?? 0);
        $excessPercent = floatval($item->wastage_percent ?? 5);
        
        $totalReqQty = ($orderQuantity * $cons) * (1 + ($excessPercent / 100));
        $inhouseQty = floatval($item->stocks?->sum('available_qty') ?? 0);
        $stockEntryDate = $item->latestStock?->created_at?->format('d-m-Y') ?? '';
        $shortageQty = $inhouseQty - $totalReqQty;
        $unitPrice = floatval($item->unit_price ?? 0);
        $totalBudget = $totalReqQty * $unitPrice;

        return (object)[
            'excessPercent' => $excessPercent,
            'totalReqQty' => $totalReqQty,
            'inhouseQty' => $inhouseQty,
            'shortageQty' => $shortageQty,
            'unitPrice' => $unitPrice,
            'totalBudget' => $totalBudget,
            'grn_date' => $stockEntryDate,
        ];
    };
@endphp

    <!-- HEADER / META INFORMATION MATRIX -->
    <table class="header-table">
        <tr>
            <td width="75%" style="vertical-align: top;">
                <div class="company-name">{{ tenant()->company_name ?? 'Factory / Company Name' }}</div>
                <div class="sheet-title">Production Bill of Materials (BOM)</div>
                
                <table width="100%" style="margin-top: 6px;" cellpadding="1.5">
                    <tr>
                        <td class="meta-label" width="15%">Style Number:</td>
                        <td width="35%"><b>{{ $style->style_number }}</b></td>
                        <td class="meta-label" width="15%">Buyer:</td>
                        <td width="35%">{{ $style->buyer->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Product Name:</td>
                        <td>{{ $style->product_name }}</td>
                        <td class="meta-label">Season:</td>
                        <td>{{ $style->season->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Order Quantity:</td>
                        <td style="font-weight: bold; color: #4f46e5;">{{ number_format($orderQuantity) }} Pcs</td>
                        <td class="meta-label">Export Date:</td>
                        <td>{{ date('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </td>
            <td width="25%" class="text-right" style="vertical-align: top;">
                @php
                    $fullPath = storage_path('app/public/' . $style->product_image);
                @endphp
                @if($style->product_image && file_exists($fullPath))
                    <img src="{{ $fullPath }}" class="product-img" alt="Product Image">
                @endif
            </td>
        </tr>
    </table>

    <!-- MAIN PRODUCTION BOM TABLE -->
    <table class="cost-table">
        <thead>
            <tr>
                <th width="18%">Material Description</th>
                <th width="8%">GMT Color</th>
                <th width="8%">Mat. Color</th>
                <th width="6%" class="text-right">GMT Order Qty</th>
                <th width="6%" class="text-right">Cons/GMT</th>
                <th width="6%" class="text-right">Excess %</th>
                <th width="8%" class="text-right">Total Req Qty</th>
                <th width="8%" class="text-right">In-House Qty</th>
                <th width="8%" class="text-right">Short/Excess</th>
                <th width="7%" class="text-center">GRN Date</th>
                <th width="7%" class="text-center">PCD Date</th>
                <th width="5%" class="text-center">Status</th>
                <th width="6%" class="text-right">Unit Price</th>
                <th width="6%" class="text-right">TTL Budget</th>
            </tr>
        </thead>
        <tbody>

            <!-- 1. FABRICS SECTION -->
            <tr class="bg-section-header">
                <td colspan="14">1. Fabric Materials</td>
            </tr>
            @php $ttlFabricBudget = 0; $ttlFabricReq = 0; @endphp
            @forelse($fabrics as $item)
            @php
                $data = $calcRowData($item);
                $ttlFabricBudget += $data->totalBudget;
                $ttlFabricReq += $data->totalReqQty;
            @endphp
            <tr>
                <td class="font-bold">{{ $item->item_description ?? $item->itemMaster->name ?? 'Fabric' }}</td>
                <td>{{ $item->gmt_color ?? 'All' }}</td>
                <td>{{ $item->material_color ?? 'DTM' }}</td>
                <td class="text-right font-mono">{{ number_format($orderQuantity) }}</td>
                <td class="text-right font-mono">{{ number_format($item->consumption, 4) }}</td>
                <td class="text-right font-mono">{{ number_format($data->excessPercent, 2) }}%</td>
                <td class="text-right font-mono font-bold" style="color: #4338ca;">{{ number_format($data->totalReqQty, 2) }}</td>
                <td class="text-right font-mono font-bold" style="color: #047857;">{{ number_format($data->inhouseQty, 2) }}</td>
                <td class="text-right font-mono font-bold {{ $data->shortageQty < 0 ? 'color: #dc2626;' : '' }}">
                    {{ number_format($data->shortageQty, 2) }}
                </td>
                <td class="text-center font-mono">{{ $data->grn_date ? \Carbon\Carbon::parse($data->grn_date)->format('Y-m-d') : 'TBC' }}</td>
                <td class="text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                <td class="text-center">
                    @if($data->shortageQty >= 0 && $data->inhouseQty > 0)
                        <span class="status-badge status-inhouse">Done</span>
                    @elseif($data->inhouseQty > 0)
                        <span class="status-badge status-partial">Partial</span>
                    @else
                        <span class="status-badge status-pending">Pending</span>
                    @endif
                </td>
                <td class="text-right font-mono">{{ $currencySymbol }}{{ number_format($data->unitPrice, 2) }}</td>
                <td class="text-right font-mono font-bold">{{ $currencySymbol }}{{ number_format($data->totalBudget, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="color: #94a3b8; font-style: italic;">No fabric items listed.</td>
            </tr>
            @endforelse

            <!-- TOTAL FABRIC COST -->
            <tr class="bg-ttl-section">
                <td colspan="6" class="text-right">TTL FABRIC REQUIREMENT & BUDGET</td>
                <td class="text-right font-mono">{{ number_format($ttlFabricReq, 2) }}</td>
                <td colspan="6"></td>
                <td class="text-right font-mono">{{ $currencySymbol }}{{ number_format($ttlFabricBudget, 2) }}</td>
            </tr>

            <!-- 2. TRIMS & ACCESSORIES SECTION -->
            <tr class="bg-section-header">
                <td colspan="14">2. Trims & Accessories</td>
            </tr>
            @php $ttlTrimBudget = 0; $ttlTrimReq = 0; @endphp
            @forelse($trims as $item)
            @php
                $data = $calcRowData($item);
                $ttlTrimBudget += $data->totalBudget;
                $ttlTrimReq += $data->totalReqQty;
            @endphp
            <tr>
                <td class="font-bold">{{ $item->item_description ?? $item->itemMaster->name ?? 'Trim Item' }}</td>
                <td>{{ $item->gmt_color ?? 'All' }}</td>
                <td>{{ $item->material_color ?? 'DTM' }}</td>
                <td class="text-right font-mono">{{ number_format($orderQuantity) }}</td>
                <td class="text-right font-mono">{{ number_format($item->consumption, 4) }}</td>
                <td class="text-right font-mono">{{ number_format($data->excessPercent, 2) }}%</td>
                <td class="text-right font-mono font-bold" style="color: #4338ca;">{{ number_format($data->totalReqQty, 2) }}</td>
                <td class="text-right font-mono font-bold" style="color: #047857;">{{ number_format($data->inhouseQty, 2) }}</td>
                <td class="text-right font-mono font-bold {{ $data->shortageQty < 0 ? 'color: #dc2626;' : '' }}">
                    {{ number_format($data->shortageQty, 2) }}
                </td>
                <td class="text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                <td class="text-center font-mono">{{ $item->pcd_date ? \Carbon\Carbon::parse($item->pcd_date)->format('Y-m-d') : 'TBC' }}</td>
                <td class="text-center">
                    @if($data->shortageQty >= 0 && $data->inhouseQty > 0)
                        <span class="status-badge status-inhouse">Done</span>
                    @elseif($data->inhouseQty > 0)
                        <span class="status-badge status-partial">Partial</span>
                    @else
                        <span class="status-badge status-pending">Pending</span>
                    @endif
                </td>
                <td class="text-right font-mono">{{ $currencySymbol }}{{ number_format($data->unitPrice, 2) }}</td>
                <td class="text-right font-mono font-bold">{{ $currencySymbol }}{{ number_format($data->totalBudget, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="color: #94a3b8; font-style: italic;">No trim items listed.</td>
            </tr>
            @endforelse

            <!-- TOTAL TRIM COST -->
            <tr class="bg-ttl-section">
                <td colspan="6" class="text-right">TTL TRIM REQUIREMENT & BUDGET</td>
                <td class="text-right font-mono">{{ number_format($ttlTrimReq, 2) }}</td>
                <td colspan="6"></td>
                <td class="text-right font-mono">{{ $currencySymbol }}{{ number_format($ttlTrimBudget, 2) }}</td>
            </tr>

            <!-- GRAND TOTAL MATERIAL COST -->
            <tr class="bg-grand-total">
                <td colspan="13" class="text-right uppercase">Total Material Budget (Fabric + Trims)</td>
                <td class="text-right font-mono">{{ $currencySymbol }}{{ number_format($ttlFabricBudget + $ttlTrimBudget, 2) }}</td>
            </tr>

        </tbody>
    </table>

</body>
</html>