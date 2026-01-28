<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $sale->invoice ?? $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .shop-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .shop-info {
            font-size: 10px;
            color: #333;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .double-divider {
            border-top: 2px solid #000;
            margin: 8px 0;
        }
        .receipt-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 5px 0;
        }
        .info-section {
            margin: 8px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 2px 0;
        }
        .info-row strong {
            font-weight: bold;
        }
        .items-section {
            margin: 10px 0;
        }
        .items-header {
            display: flex;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 10px;
        }
        .col-item { flex: 1; }
        .col-qty { width: 35px; text-align: center; }
        .col-price { width: 50px; text-align: right; }
        .col-total { width: 60px; text-align: right; }

        .item-row {
            display: flex;
            margin: 3px 0;
            font-size: 11px;
        }
        .item-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .addon-row {
            display: flex;
            font-size: 10px;
            color: #555;
            margin: 1px 0 1px 10px;
        }
        .addon-name {
            flex: 1;
        }
        .totals-section {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }
        .total-row.discount {
            color: #060;
        }
        .total-row.grand {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin-top: 5px;
        }
        .payment-section {
            margin-top: 10px;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 3px;
        }
        .payment-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        .thank-you {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer-info {
            font-size: 10px;
            color: #555;
        }
        .order-type {
            display: inline-block;
            padding: 2px 8px;
            background: #333;
            color: #fff;
            font-size: 10px;
            border-radius: 3px;
            margin-top: 8px;
        }
        .print-actions {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .print-actions button {
            padding: 10px 30px;
            font-size: 14px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .btn-print {
            background: #28a745;
            color: white;
        }
        .btn-close {
            background: #6c757d;
            color: white;
        }
        @media print {
            body {
                width: 80mm;
                padding: 0 3mm;
            }
            .print-actions {
                display: none !important;
            }
            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions (hidden when printing) -->
    <div class="print-actions">
        <button type="button" class="btn-print" onclick="window.print()">Print Receipt</button>
        <button type="button" class="btn-close" onclick="window.close()">Close</button>
    </div>

    <!-- Receipt Header -->
    <div class="header">
        <div class="shop-name">{{ $setting->app_name ?? $setting->company_name ?? 'Sky Beach' }}</div>
        <div class="shop-info">
            @if($setting->address ?? null)
            <div>{{ $setting->address }}</div>
            @endif
            @if($setting->mobile ?? $setting->phone ?? null)
            <div>Tel: {{ $setting->mobile ?? $setting->phone }}</div>
            @endif
        </div>
    </div>

    <div class="double-divider"></div>

    <div class="receipt-title">RECEIPT</div>

    <div class="divider"></div>

    <!-- Order Info -->
    <div class="info-section">
        <div class="info-row">
            <span>Invoice:</span>
            <span><strong>{{ $sale->invoice ?? '#' . str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
        </div>
        <div class="info-row">
            <span>Date:</span>
            <span>{{ $sale->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        @if($sale->table)
        <div class="info-row">
            <span>Table:</span>
            <span>{{ $sale->table->name }}</span>
        </div>
        @endif
        @if($sale->customer && $sale->customer->name != 'Guest')
        <div class="info-row">
            <span>Customer:</span>
            <span>{{ $sale->customer->name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span>Cashier:</span>
            <span>{{ $sale->createdBy->name ?? 'Staff' }}</span>
        </div>
        @if($sale->waiter)
        <div class="info-row">
            <span>Waiter:</span>
            <span>{{ $sale->waiter->name }}</span>
        </div>
        @endif
        @if($sale->guest_count > 1)
        <div class="info-row">
            <span>Guests:</span>
            <span>{{ $sale->guest_count }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Items -->
    <div class="items-section">
        <div class="items-header">
            <span class="col-item">Item</span>
            <span class="col-qty">Qty</span>
            <span class="col-price">Price</span>
            <span class="col-total">Total</span>
        </div>
        @foreach($sale->details as $detail)
        <div class="item-row">
            <span class="col-item item-name">{{ $detail->menuItem->name ?? ($detail->service->name ?? ($detail->ingredient->name ?? 'Item')) }}</span>
            <span class="col-qty">{{ $detail->quantity }}</span>
            <span class="col-price">{{ number_format($detail->price, 2) }}</span>
            <span class="col-total">{{ number_format($detail->sub_total, 2) }}</span>
        </div>
        @if($detail->attributes)
        <div class="addon-row">
            <span class="addon-name" style="color: #666;">{{ $detail->attributes }}</span>
        </div>
        @endif
        @if(!empty($detail->addons))
            @php
                $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons;
            @endphp
            @if(is_array($addons) && count($addons) > 0)
                @foreach($addons as $addon)
                <div class="addon-row">
                    <span class="addon-name">+ {{ $addon['name'] }}</span>
                    <span class="col-qty">{{ $addon['qty'] ?? 1 }}</span>
                    <span class="col-price">{{ number_format($addon['price'] ?? 0, 2) }}</span>
                    <span class="col-total">{{ number_format(($addon['price'] ?? 0) * ($addon['qty'] ?? 1), 2) }}</span>
                </div>
                @endforeach
            @endif
        @endif
        @endforeach
    </div>

    <!-- Totals -->
    <div class="totals-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ number_format($sale->total_price, 2) }}</span>
        </div>
        @if(($sale->order_discount ?? 0) > 0)
        <div class="total-row discount">
            <span>Discount:</span>
            <span>-{{ number_format($sale->order_discount, 2) }}</span>
        </div>
        @endif
        @if(($sale->total_tax ?? 0) > 0)
        <div class="total-row">
            <span>Tax @if(($sale->tax_rate ?? 0) > 0)({{ $sale->tax_rate }}%)@endif:</span>
            <span>{{ number_format($sale->total_tax, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span>TOTAL:</span>
            <span>{{ number_format($sale->grand_total, 2) }}</span>
        </div>
    </div>

    <!-- Payment Info -->
    @if($sale->payment && $sale->payment->count() > 0)
    <div class="payment-section">
        <div class="payment-title">PAYMENT</div>
        @foreach($sale->payment as $payment)
        <div class="info-row">
            <span>{{ ucfirst($payment->account->account_type ?? $payment->payment_type ?? 'Cash') }}:</span>
            <span>{{ number_format($payment->paying_amount ?? $payment->amount ?? 0, 2) }}</span>
        </div>
        @endforeach
        @if(($sale->paid_amount ?? 0) > 0)
        <div class="info-row">
            <span>Paid:</span>
            <span>{{ number_format($sale->paid_amount, 2) }}</span>
        </div>
        @endif
        @if(($sale->return_amount ?? 0) > 0)
        <div class="info-row">
            <span>Change:</span>
            <span>{{ number_format($sale->return_amount, 2) }}</span>
        </div>
        @endif
    </div>
    @else
        @if(($sale->paid_amount ?? 0) > 0)
        <div class="payment-section">
            <div class="payment-title">PAYMENT</div>
            <div class="info-row">
                <span>Paid:</span>
                <span>{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if(($sale->return_amount ?? 0) > 0)
            <div class="info-row">
                <span>Change:</span>
                <span>{{ number_format($sale->return_amount, 2) }}</span>
            </div>
            @endif
        </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="thank-you">Thank You!</div>
        <div class="footer-info">Please come again</div>
        @if($sale->order_type)
        <div class="order-type">{{ ucfirst(str_replace('_', ' ', $sale->order_type)) }}</div>
        @endif
        @if($setting->website ?? null)
        <div class="footer-info" style="margin-top: 8px;">{{ $setting->website }}</div>
        @endif
        <div class="footer-info" style="margin-top: 8px; font-size: 9px;">
            Printed: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <script>
        // Auto-print on load if coming from POS
        if (window.location.search.includes('auto=1')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 300);
            }
        }
    </script>
</body>
</html>
