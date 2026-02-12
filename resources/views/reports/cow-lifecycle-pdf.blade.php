<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Cow Lifecycle Report – {{ $animal->animal_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 24px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .label { width: 50%; }
        .amount { text-align: right; }
        .total-row { font-weight: bold; background: #f9f9f9; }
        .profit { color: #0a0; }
        .loss { color: #c00; }
        .footer { margin-top: 24px; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <h1>Cow Lifecycle Report</h1>
    <p class="subtitle">{{ $animal->animal_id }} · {{ $animal->breed }} · Status: {{ $animal->status->label() }}</p>

    <table>
        <tr>
            <th class="label">Item</th>
            <th class="amount">Amount (BDT)</th>
        </tr>
        <tr>
            <td>Purchase date</td>
            <td class="amount">{{ $lifecycle['purchase_date'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>Purchase price</td>
            <td class="amount">{{ number_format($lifecycle['purchase_price'], 2) }}</td>
        </tr>
        <tr>
            <td>Medicine cost</td>
            <td class="amount">{{ number_format($lifecycle['medicine'], 2) }}</td>
        </tr>
        <tr>
            <td>Labour cost (allocated)</td>
            <td class="amount">{{ number_format($lifecycle['labour_allocated'], 2) }}</td>
        </tr>
        <tr>
            <td>Feeds cost (fodder + concentrate)</td>
            <td class="amount">{{ number_format($lifecycle['feeds'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total investment</td>
            <td class="amount">{{ number_format($lifecycle['total_invest'], 2) }}</td>
        </tr>
        <tr>
            <td>Selling price</td>
            <td class="amount">{{ $lifecycle['is_sold'] ? number_format($lifecycle['selling_price'], 2) : 'Not sold' }}</td>
        </tr>
        <tr class="total-row">
            <td>Profit / Loss</td>
            <td class="amount {{ $lifecycle['profit_loss'] >= 0 ? 'profit' : 'loss' }}">
                @if($lifecycle['is_sold'])
                    {{ number_format($lifecycle['profit_loss'], 2) }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    <p class="footer">Generated on {{ now()->format('Y-m-d H:i') }} · Farmlytics</p>
</body>
</html>
