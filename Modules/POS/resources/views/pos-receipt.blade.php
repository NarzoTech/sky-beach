<div class="pos-receipt" id="pos-receipt-content">
    <style>
        .pos-receipt {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
        }
        .pos-receipt .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .pos-receipt .shop-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .pos-receipt .shop-info {
            font-size: 11px;
            color: #333;
        }
        .pos-receipt .receipt-info {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .pos-receipt .receipt-info div {
            display: flex;
            justify-content: space-between;
        }
        .pos-receipt .items-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .pos-receipt .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        .pos-receipt .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .pos-receipt .items-table .text-right {
            text-align: right;
        }
        .pos-receipt .items-table .text-center {
            text-align: center;
        }
        .pos-receipt .totals {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 12px;
        }
        .pos-receipt .totals div {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .pos-receipt .totals .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px dashed #000;
            margin-top: 5px;
            padding-top: 5px;
        }
        .pos-receipt .payment-info {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 12px;
        }
        .pos-receipt .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            margin-top: 15px;
            padding-top: 10px;
            font-size: 11px;
        }
        .pos-receipt .thank-you {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        @media print {
            .pos-receipt {
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Receipt Header -->
    <div class="receipt-header">
        <div class="shop-name">{{ $setting->app_name ?? 'Sky Beach' }}</div>
        <div class="shop-info">
            @if($setting->address ?? null)
            <div>{{ $setting->address }}</div>
            @endif
            @if($setting->mobile ?? null)
            <div>Tel: {{ $setting->mobile }}</div>
            @endif
        </div>
    </div>

    <!-- Receipt Info -->
    <div class="receipt-info">
        <div>
            <span>Invoice:</span>
            <span><strong>{{ $sale->invoice }}</strong></span>
        </div>
        <div>
            <span>Date:</span>
            <span>{{ $sale->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        @if($sale->table)
        <div>
            <span>Table:</span>
            <span>{{ $sale->table->name }}</span>
        </div>
        @endif
        @if($sale->customer && $sale->customer->name != 'Guest')
        <div>
            <span>Customer:</span>
            <span>{{ $sale->customer->name }}</span>
        </div>
        @endif
        <div>
            <span>Cashier:</span>
            <span>{{ $sale->createdBy->name ?? 'Staff' }}</span>
        </div>
    </div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%">Item</th>
                <th class="text-center" style="width: 15%">Qty</th>
                <th class="text-right" style="width: 15%">Price</th>
                <th class="text-right" style="width: 20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr>
                <td>
                    {{ $detail->menuItem->name ?? ($detail->service->name ?? ($detail->ingredient->name ?? 'Item')) }}
                    @if($detail->attributes)
                    <br><small style="color: #666;">{{ $detail->attributes }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-right">{{ number_format($detail->price, 2) }}</td>
                <td class="text-right">{{ number_format($detail->sub_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div>
            <span>Subtotal:</span>
            <span>{{ currency($sale->total_price) }}</span>
        </div>
        @if($sale->order_discount > 0)
        <div>
            <span>Discount:</span>
            <span>-{{ currency($sale->order_discount) }}</span>
        </div>
        @endif
        @if($sale->total_tax > 0)
        <div>
            <span>Tax:</span>
            <span>{{ currency($sale->total_tax) }}</span>
        </div>
        @endif
        <div class="grand-total">
            <span>TOTAL:</span>
            <span>{{ currency($sale->grand_total) }}</span>
        </div>
    </div>

    <!-- Payment Info -->
    <div class="payment-info">
        <div style="display: flex; justify-content: space-between;">
            <span>Paid:</span>
            <span>{{ currency($sale->paid_amount) }}</span>
        </div>
        @if($sale->return_amount > 0)
        <div style="display: flex; justify-content: space-between;">
            <span>Change:</span>
            <span>{{ currency($sale->return_amount) }}</span>
        </div>
        @endif
        @if($sale->payment && $sale->payment->count() > 0)
        <div style="margin-top: 5px; font-size: 11px; color: #666;">
            Payment:
            @foreach($sale->payment as $payment)
                {{ ucfirst($payment->account->account_type ?? 'cash') }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="thank-you">Thank You!</div>
        <div>Please come again</div>
        @if($sale->order_type)
        <div style="margin-top: 5px;">
            <strong>{{ ucfirst(str_replace('_', ' ', $sale->order_type)) }}</strong>
        </div>
        @endif
    </div>
</div>
