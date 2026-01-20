<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Order') }} #{{ $order->invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
            max-width: 80mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .order-info {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .order-info table {
            width: 100%;
        }

        .order-info td {
            padding: 3px 0;
        }

        .order-info .label {
            color: #666;
        }

        .order-type {
            display: inline-block;
            padding: 3px 8px;
            background: #333;
            color: #fff;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .customer-info {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .customer-info h3 {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .items {
            margin-bottom: 15px;
        }

        .items h3 {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .items table {
            width: 100%;
            border-collapse: collapse;
        }

        .items th, .items td {
            padding: 5px 3px;
            text-align: left;
        }

        .items th {
            border-bottom: 1px solid #333;
            font-weight: bold;
        }

        .items td {
            border-bottom: 1px dotted #ddd;
            vertical-align: top;
        }

        .items .item-name {
            max-width: 120px;
        }

        .items .item-addons {
            font-size: 10px;
            color: #666;
            display: block;
        }

        .items .item-note {
            font-size: 10px;
            color: #999;
            font-style: italic;
            display: block;
        }

        .items .text-right {
            text-align: right;
        }

        .items .text-center {
            text-align: center;
        }

        .totals {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .totals table {
            width: 100%;
        }

        .totals td {
            padding: 3px 0;
        }

        .totals .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .totals .text-right {
            text-align: right;
        }

        .delivery-info {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .delivery-info h3 {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .notes {
            margin-bottom: 15px;
            padding: 10px;
            background: #fff3cd;
            border-radius: 5px;
        }

        .notes h3 {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-pending { background: #ffc107; color: #333; }
        .status-confirmed { background: #17a2b8; color: #fff; }
        .status-preparing { background: #007bff; color: #fff; }
        .status-ready { background: #28a745; color: #fff; }
        .status-out_for_delivery { background: #17a2b8; color: #fff; }
        .status-delivered, .status-completed { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Print Order
        </button>
    </div>

    <div class="header">
        <h1>{{ config('app.name', 'Restaurant') }}</h1>
        <p>{{ __('Order Receipt') }}</p>
    </div>

    <div class="order-info">
        <table>
            <tr>
                <td class="label">{{ __('Order #') }}:</td>
                <td><strong>{{ $order->invoice }}</strong></td>
            </tr>
            <tr>
                <td class="label">{{ __('Date') }}:</td>
                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Type') }}:</td>
                <td>
                    <span class="order-type">
                        {{ $order->order_type === 'delivery' ? __('DELIVERY') : __('TAKE AWAY') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">{{ __('Status') }}:</td>
                <td>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ strtoupper($order->status_label) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    @php
        $notes = json_decode($order->notes ?? '{}', true);
    @endphp

    <div class="customer-info">
        <h3>{{ __('Customer') }}</h3>
        <p><strong>{{ $notes['customer_name'] ?? ($order->customer->name ?? 'Guest') }}</strong></p>
        @if(isset($notes['customer_phone']) || $order->delivery_phone)
            <p>{{ $notes['customer_phone'] ?? $order->delivery_phone }}</p>
        @endif
        @if(isset($notes['customer_email']))
            <p>{{ $notes['customer_email'] }}</p>
        @endif
    </div>

    @if($order->order_type === 'delivery' && $order->delivery_address)
        <div class="delivery-info">
            <h3>{{ __('Delivery Address') }}</h3>
            <p>{{ $order->delivery_address }}</p>
            @if($order->delivery_notes)
                <p><em>{{ $order->delivery_notes }}</em></p>
            @endif
        </div>
    @endif

    <div class="items">
        <h3>{{ __('Order Items') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th class="text-center">{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $item)
                    <tr>
                        <td class="item-name">
                            {{ $item->menuItem->name ?? 'Item' }}
                            @if($item->addons && count($item->addons) > 0)
                                <span class="item-addons">+ {{ collect($item->addons)->pluck('name')->implode(', ') }}</span>
                            @endif
                            @if($item->note)
                                <span class="item-note">Note: {{ $item->note }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td>{{ __('Subtotal') }}:</td>
                <td class="text-right">${{ number_format($order->total_price, 2) }}</td>
            </tr>
            @if($order->shipping_cost > 0)
                <tr>
                    <td>{{ __('Delivery Fee') }}:</td>
                    <td class="text-right">${{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
            @endif
            @if($order->total_tax > 0)
                <tr>
                    <td>{{ __('Tax') }}:</td>
                    <td class="text-right">${{ number_format($order->total_tax, 2) }}</td>
                </tr>
            @endif
            @if($order->discount_amount > 0)
                <tr>
                    <td>{{ __('Discount') }}:</td>
                    <td class="text-right">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>{{ __('TOTAL') }}:</td>
                <td class="text-right">${{ number_format($order->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin-bottom: 15px;">
        <p>
            <strong>{{ __('Payment') }}:</strong>
            {{ is_array($order->payment_method) && in_array('cash', $order->payment_method) ? __('Cash') : __('Card') }}
            ({{ ucfirst($order->payment_status) }})
        </p>
    </div>

    @if($order->special_instructions)
        <div class="notes">
            <h3>{{ __('Special Instructions') }}</h3>
            <p>{{ $order->special_instructions }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ __('Thank you for your order!') }}</p>
        <p>{{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>
