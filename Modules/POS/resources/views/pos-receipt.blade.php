<div class="pos-receipt" id="pos-receipt-content">
    <style>
        .pos-receipt {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            color: #000;
            font-size: 13px;
            font-weight: 500;
        }
        .pos-receipt .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .pos-receipt .shop-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #000;
        }
        .pos-receipt .shop-info {
            font-size: 13px;
            font-weight: 500;
            color: #000;
        }
        .pos-receipt .receipt-info {
            font-size: 13px;
            font-weight: 500;
            color: #000;
            margin-bottom: 10px;
        }
        .pos-receipt .receipt-info div {
            display: flex;
            justify-content: space-between;
        }
        .pos-receipt .receipt-info strong {
            font-weight: 700;
        }
        .pos-receipt .items-table {
            width: 100%;
            font-size: 13px;
            font-weight: 500;
            color: #000;
            border-collapse: collapse;
        }
        .pos-receipt .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            font-weight: 700;
            color: #000;
        }
        .pos-receipt .items-table td {
            padding: 4px 0;
            vertical-align: top;
            font-weight: 500;
            color: #000;
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
            font-size: 13px;
            font-weight: 500;
            color: #000;
        }
        .pos-receipt .totals div {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .pos-receipt .totals .grand-total {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin-top: 5px;
            padding: 8px 0;
        }
        .pos-receipt .payment-info {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #000;
        }
        .pos-receipt .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            margin-top: 15px;
            padding-top: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #000;
        }
        .pos-receipt .thank-you {
            font-size: 16px;
            font-weight: 700;
            color: #000;
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
            <span>{{ $sale->created_at->setTimezone('Asia/Dhaka')->format('d/m/Y h:i A') }}</span>
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
        @if($sale->waiter)
        <div>
            <span>Waiter:</span>
            <span>{{ $sale->waiter->name }}</span>
        </div>
        @endif
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
                    <br><small>{{ $detail->attributes }}</small>
                    @endif
                    @if(!empty($detail->addons))
                    <br><small>+ @foreach($detail->addons as $addon){{ $addon['name'] }}@if(!$loop->last), @endif @endforeach</small>
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
        @php
            $payments = $sale->payment ?? collect();
            $paymentCount = $payments->count();
            $isSplitPayment = $paymentCount > 1;
            $returnAmount = ($sale->paid_amount ?? 0) - ($sale->grand_total ?? 0);
            $hasCashPayment = $payments->contains(function($p) {
                return strtolower($p->account->account_type ?? 'cash') === 'cash';
            });
        @endphp
        @if($isSplitPayment)
            @foreach($payments as $p)
            <div style="display: flex; justify-content: space-between;">
                <span>{{ ucfirst(str_replace('_', ' ', $p->account->account_type ?? 'cash')) }}:</span>
                <span>{{ currency($p->amount) }}</span>
            </div>
            @endforeach
            @if($hasCashPayment && $returnAmount > 0)
            <div style="display: flex; justify-content: space-between; margin-top: 5px; padding-top: 5px; border-top: 1px dashed #000;">
                <span>Return:</span>
                <span>{{ currency($returnAmount) }}</span>
            </div>
            @endif
        @else
            @php
                $firstPayment = $payments->first();
                $paymentMethod = $firstPayment ? ucfirst(str_replace('_', ' ', $firstPayment->account->account_type ?? 'cash')) : 'Cash';
                $isCashPayment = $firstPayment ? strtolower($firstPayment->account->account_type ?? 'cash') === 'cash' : true;
            @endphp
            @if($isCashPayment)
            <div style="display: flex; justify-content: space-between;">
                <span>Received:</span>
                <span>{{ currency($sale->paid_amount) }}</span>
            </div>
            @if($returnAmount > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Return:</span>
                <span>{{ currency($returnAmount) }}</span>
            </div>
            @endif
            @endif
            <div style="display: flex; justify-content: space-between;{{ $isCashPayment ? ' margin-top: 5px; padding-top: 5px; border-top: 1px dashed #000;' : '' }}">
                <span>Payment By:</span>
                <span>{{ $paymentMethod }}</span>
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
