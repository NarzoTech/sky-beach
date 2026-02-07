<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Slip #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 0;
            size: {{ optional($printer)->paper_width ?? 80 }}mm auto;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            max-width: {{ optional($printer)->paper_width ?? 80 }}mm;
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 3mm;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .restaurant-name {
            font-size: 16px;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 2px 0;
        }
        .items-table {
            width: 100%;
            margin: 8px 0;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 10px;
            padding: 3px 0;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .items-table .qty {
            width: 30px;
            text-align: center;
        }
        .items-table .price {
            width: 60px;
            text-align: right;
        }
        .addon-row {
            font-size: 10px;
            color: #555;
        }
        .addon-row td {
            padding: 1px 0 1px 10px;
        }
        .totals {
            margin-top: 5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }
        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin-top: 5px;
        }
        .status {
            text-align: center;
            padding: 8px;
            margin-top: 8px;
            border: 2px solid #000;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 10px;
        }
        .no-print {
            text-align: center;
            padding: 8px 0;
            border-top: 1px dashed #ccc;
            margin-top: 10px;
        }
        .no-print button {
            background: #333;
            color: #fff;
            border: none;
            padding: 6px 20px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            cursor: pointer;
            border-radius: 3px;
        }
        .no-print button:hover {
            background: #000;
        }
        @media print {
            .no-print { display: none; }
            html, body {
                width: {{ optional($printer)->paper_width ?? 80 }}mm;
                max-width: {{ optional($printer)->paper_width ?? 80 }}mm;
                padding: 2mm;
            }
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

    <div class="divider"></div>

    <div class="info-row">
        <span>Order #:</span>
        <span><strong>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
    </div>
    @if($sale->table)
    <div class="info-row">
        <span>Table:</span>
        <span><strong>{{ $sale->table->name }}</strong></span>
    </div>
    @endif
    <div class="info-row">
        <span>Waiter:</span>
        <span>{{ $sale->waiter->name ?? 'N/A' }}</span>
    </div>
    <div class="info-row">
        <span>Date:</span>
        <span>{{ $sale->created_at->format('d-M-Y H:i') }}</span>
    </div>
    @if($sale->guest_count)
    <div class="info-row">
        <span>Guests:</span>
        <span>{{ $sale->guest_count }}</span>
    </div>
    @endif

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr>
                <td>{{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}</td>
                <td class="qty">{{ $detail->quantity }}</td>
                <td class="price">{{ number_format($detail->price * $detail->quantity, 2) }}</td>
            </tr>
            @if($detail->addons)
                @php $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons; @endphp
                @if(is_array($addons))
                    @foreach($addons as $addon)
                    <tr class="addon-row">
                        <td>+ {{ $addon['name'] }}</td>
                        <td class="qty">{{ $addon['qty'] ?? 1 }}</td>
                        <td class="price">{{ number_format(($addon['price'] ?? 0) * ($addon['qty'] ?? 1), 2) }}</td>
                    </tr>
                    @endforeach
                @endif
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ number_format($sale->total_price ?? 0, 2) }}</span>
        </div>
        @if(($sale->total_tax ?? 0) > 0)
        <div class="total-row">
            <span>Tax:</span>
            <span>{{ number_format($sale->total_tax, 2) }}</span>
        </div>
        @endif
        @if(($sale->discount_amount ?? 0) > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>-{{ number_format($sale->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span>TOTAL:</span>
            <span>{{ number_format($sale->grand_total ?? $sale->total_price ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="status">
        ** {{ $sale->payment_status == 1 ? 'PAID' : 'PAYMENT PENDING' }} **
    </div>

    <div class="footer">
        <div>Thank you for dining with us!</div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">&#x1F5A8; Print</button>
    </div>
</body>
</html>
