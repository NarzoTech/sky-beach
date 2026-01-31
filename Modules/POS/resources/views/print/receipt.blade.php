<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: {{ $printer->paper_width ?? 80 }}mm;
            padding: 3mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .restaurant-name {
            font-size: 18px;
            font-weight: bold;
        }
        .restaurant-info {
            font-size: 12px;
            margin-top: 3px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px solid #000;
            margin: 8px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 2px 0;
        }
        .items-table {
            width: 100%;
            margin: 8px 0;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 12px;
            padding: 3px 0;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 12px;
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
            font-size: 12px;
            color: #000;
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
            font-size: 12px;
            padding: 2px 0;
        }
        .total-row.grand {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin-top: 5px;
        }
        .payment-info {
            margin-top: 10px;
            padding: 8px;
            background: #f5f5f5;
        }
        .payment-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
        }
        .footer-thanks {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        @media print {
            body {
                width: {{ $printer->paper_width ?? 80 }}mm;
            }
            @page {
                margin: 0;
                size: {{ $printer->paper_width ?? 80 }}mm auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="restaurant-name">{{ $setting->company_name ?? 'Restaurant' }}</div>
        @if($setting->address ?? false)
        <div class="restaurant-info">{{ $setting->address }}</div>
        @endif
        @if($setting->phone ?? false)
        <div class="restaurant-info">Tel: {{ $setting->phone }}</div>
        @endif
    </div>

    <div class="double-divider"></div>

    <div style="text-align: center; font-weight: bold; margin: 5px 0;">
        TAX INVOICE / RECEIPT
    </div>

    <div class="divider"></div>

    <div class="info-row">
        <span>Receipt #:</span>
        <span><strong>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
    </div>
    <div class="info-row">
        <span>Date:</span>
        <span>{{ $sale->created_at->setTimezone('Asia/Dhaka')->format('d-M-Y H:i') }}</span>
    </div>
    @if($sale->table)
    <div class="info-row">
        <span>Table:</span>
        <span>{{ $sale->table->name }}</span>
    </div>
    @endif
    <div class="info-row">
        <span>Served by:</span>
        <span>{{ $sale->waiter->name ?? 'N/A' }}</span>
    </div>
    @if($sale->customer)
    <div class="info-row">
        <span>Customer:</span>
        <span>{{ $sale->customer->name }}</span>
    </div>
    @endif

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="qty">Qty</th>
                <th>Description</th>
                <th class="price">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr>
                <td class="qty">{{ $detail->quantity }}</td>
                <td>{{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}</td>
                <td class="price">{{ number_format($detail->price * $detail->quantity, 2) }}</td>
            </tr>
            @if($detail->addons)
                @php $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons; @endphp
                @if(is_array($addons))
                    @foreach($addons as $addon)
                    <tr class="addon-row">
                        <td class="qty">{{ $addon['qty'] ?? 1 }}</td>
                        <td>+ {{ $addon['name'] }}</td>
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
            <span>{{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->tax_amount > 0)
        <div class="total-row">
            <span>Tax ({{ $sale->tax_rate ?? 0 }}%):</span>
            <span>{{ number_format($sale->tax_amount, 2) }}</span>
        </div>
        @endif
        @if($sale->discount_amount > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>-{{ number_format($sale->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span>GRAND TOTAL:</span>
            <span>{{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    @php
        $totalAmount = $sale->total ?? $sale->grand_total ?? 0;
        $paidAmount = $sale->paid_amount ?? 0;
        $returnAmount = $paidAmount - $totalAmount;
        $paymentMethod = 'Cash';
        $isCashPayment = true;
        if($sale->payments && $sale->payments->count() > 0) {
            $firstPayment = $sale->payments->first();
            $paymentMethod = ucfirst($firstPayment->payment_type ?? 'cash');
            $isCashPayment = strtolower($firstPayment->payment_type ?? 'cash') == 'cash';
        }
    @endphp
    @if($sale->payments && $sale->payments->count() > 0)
    <div class="payment-info">
        <div class="payment-title">PAYMENT DETAILS</div>
        @if($isCashPayment)
        <div class="info-row">
            <span>Received:</span>
            <span>{{ currency($paidAmount) }}</span>
        </div>
        @if($returnAmount > 0)
        <div class="info-row">
            <span>Return:</span>
            <span>{{ currency($returnAmount) }}</span>
        </div>
        @endif
        @endif
        <div class="info-row"{{ $isCashPayment ? ' style="border-top: 1px dashed #000; margin-top: 3px; padding-top: 3px;"' : '' }}>
            <span>Payment By:</span>
            <span>{{ $paymentMethod }}</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="footer-thanks">Thank You For Dining With Us!</div>
        <div>Please Come Again</div>
        @if($setting->website ?? false)
        <div>{{ $setting->website }}</div>
        @endif
        <div style="margin-top: 8px;">
            Printed: {{ now()->setTimezone('Asia/Dhaka')->format('d-M-Y H:i:s') }}
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
