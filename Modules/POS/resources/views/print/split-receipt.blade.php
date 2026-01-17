<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Split Receipt - {{ $split->label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            padding: 3mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .restaurant-name { font-size: 16px; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .double-divider { border-top: 2px solid #000; margin: 8px 0; }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 2px 0;
        }
        .items-table { width: 100%; margin: 8px 0; }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 10px;
            padding: 3px 0;
        }
        .items-table td {
            padding: 3px 0;
            font-size: 11px;
        }
        .items-table .qty { width: 30px; text-align: center; }
        .items-table .price { width: 60px; text-align: right; }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }
        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin-top: 5px;
        }
        .split-label {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
            border: 2px solid #000;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        @media print {
            body { width: 80mm; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="restaurant-name">{{ $setting->company_name ?? 'Restaurant' }}</div>
        @if($setting->address ?? false)
        <div style="font-size: 10px;">{{ $setting->address }}</div>
        @endif
    </div>

    <div class="split-label">
        {{ $split->label }}
    </div>

    <div class="double-divider"></div>

    <div class="info-row">
        <span>Order #:</span>
        <span><strong>{{ str_pad($split->sale_id, 6, '0', STR_PAD_LEFT) }}</strong></span>
    </div>
    @if($split->sale->table)
    <div class="info-row">
        <span>Table:</span>
        <span>{{ $split->sale->table->name }}</span>
    </div>
    @endif
    <div class="info-row">
        <span>Date:</span>
        <span>{{ now()->format('d-M-Y H:i') }}</span>
    </div>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="qty">Qty</th>
                <th>Item</th>
                <th class="price">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($split->items as $item)
            <tr>
                <td class="qty">{{ $item->quantity }}</td>
                <td>{{ $item->productSale->menuItem->name ?? 'Item' }}</td>
                <td class="price">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="total-row">
        <span>Subtotal:</span>
        <span>{{ number_format($split->subtotal, 2) }}</span>
    </div>
    @if($split->tax_amount > 0)
    <div class="total-row">
        <span>Tax:</span>
        <span>{{ number_format($split->tax_amount, 2) }}</span>
    </div>
    @endif
    <div class="total-row grand">
        <span>TOTAL:</span>
        <span>{{ number_format($split->total, 2) }}</span>
    </div>

    @if($split->payment_status === 'paid')
    <div style="text-align: center; margin-top: 10px; padding: 5px; border: 1px solid #000;">
        ** PAID - {{ ucfirst($split->payment_method ?? 'Cash') }} **
    </div>
    @else
    <div style="text-align: center; margin-top: 10px; padding: 5px; border: 2px solid #000;">
        ** PAYMENT DUE: {{ number_format($split->remaining, 2) }} **
    </div>
    @endif

    <div class="footer">
        <div>Thank You!</div>
        <div style="margin-top: 5px; font-size: 9px;">
            Printed: {{ now()->format('d-M-Y H:i:s') }}
        </div>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
