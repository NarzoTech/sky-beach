<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kitchen Order #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            width: {{ $printer->paper_width ?? 80 }}mm;
            padding: 5mm;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
        }
        .order-info {
            font-size: 16px;
            margin: 5px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .items-header {
            font-weight: bold;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }
        .item {
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }
        .item-name {
            font-size: 16px;
            font-weight: bold;
        }
        .item-qty {
            font-size: 18px;
            font-weight: bold;
        }
        .addon {
            padding-left: 15px;
            font-size: 13px;
            color: #333;
        }
        .addon::before {
            content: "+ ";
        }
        .note {
            padding-left: 15px;
            font-size: 12px;
            font-style: italic;
            color: #666;
        }
        .special-instructions {
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #000;
            background: #f5f5f5;
        }
        .special-instructions-title {
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
        .time {
            font-size: 14px;
            font-weight: bold;
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
        <div class="title">** KITCHEN ORDER **</div>
        <div class="order-info">ORDER #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($sale->table)
        <div class="order-info" style="font-size: 22px;">TABLE: {{ $sale->table->name }}</div>
        @endif
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <strong>Waiter:</strong> {{ $sale->waiter->name ?? 'N/A' }}
        </div>
        <div>
            <strong>Guests:</strong> {{ $sale->guest_count ?? 1 }}
        </div>
    </div>

    <div class="time">
        {{ $sale->created_at->format('d-M-Y H:i') }}
    </div>

    <div class="divider"></div>

    <div class="items-header">
        <span>QTY</span>
        <span>ITEM</span>
    </div>

    @foreach($sale->details as $detail)
    <div class="item">
        <div style="display: flex; gap: 10px;">
            <span class="item-qty">{{ $detail->quantity }}x</span>
            <span class="item-name">{{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}</span>
        </div>

        @if($detail->addons)
            @php $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons; @endphp
            @if(is_array($addons))
                @foreach($addons as $addon)
                <div class="addon">
                    {{ $addon['qty'] ?? 1 }}x {{ $addon['name'] }}
                </div>
                @endforeach
            @endif
        @endif

        @if($detail->note)
        <div class="note">Note: {{ $detail->note }}</div>
        @endif
    </div>
    @endforeach

    @if($sale->special_instructions)
    <div class="special-instructions">
        <div class="special-instructions-title">SPECIAL INSTRUCTIONS:</div>
        <div>{{ $sale->special_instructions }}</div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="footer">
        <strong>Total Items: {{ $sale->details->sum('quantity') }}</strong>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
