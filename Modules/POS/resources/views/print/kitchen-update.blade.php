<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Update #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            width: {{ optional($printer)->paper_width ?? 80 }}mm;
            padding: 5mm;
        }
        .header {
            text-align: center;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            background: #ffeb3b;
        }
        .title {
            font-size: 18px;
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
        .item {
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }
        .item-name {
            font-size: 16px;
            font-weight: bold;
        }
        .item-qty {
            font-size: 20px;
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
                width: {{ optional($printer)->paper_width ?? 80 }}mm;
            }
            @page {
                margin: 0;
                size: {{ optional($printer)->paper_width ?? 80 }}mm auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">** ADD TO ORDER **</div>
        <div class="order-info">ORDER #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($sale->table)
        <div class="order-info" style="font-size: 22px;">TABLE: {{ $sale->table->name }}</div>
        @endif
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <strong>Waiter:</strong> {{ $sale->waiter->name ?? 'N/A' }}
        </div>
    </div>

    <div class="time">
        {{ now()->format('d-M-Y H:i') }}
    </div>

    <div class="divider"></div>

    <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">
        NEW ITEMS ADDED:
    </div>

    @foreach($newItems as $item)
    <div class="item">
        <div style="display: flex; gap: 10px;">
            <span class="item-qty">{{ $item['qty'] }}x</span>
            <span class="item-name">{{ $item['name'] }}</span>
        </div>

        @if(!empty($item['addons']))
            @foreach($item['addons'] as $addon)
            <div class="addon">
                {{ $addon['qty'] ?? 1 }}x {{ $addon['name'] }}
            </div>
            @endforeach
        @endif

        @if(!empty($item['note']))
        <div class="note">Note: {{ $item['note'] }}</div>
        @endif
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="footer">
        <strong>New Items: {{ count($newItems) }}</strong>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
