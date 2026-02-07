<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VOID Order #{{ $sale->id }}</title>
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
            border: 3px solid #dc3545;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8d7da;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
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
            padding: 5px 0;
            text-decoration: line-through;
            color: #666;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #dc3545;
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
        <div class="title">*** VOID ***</div>
        <div class="order-info">ORDER #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($sale->table)
        <div class="order-info">TABLE: {{ $sale->table->name }}</div>
        @endif
    </div>

    <div style="margin-bottom: 10px;">
        <div><strong>Waiter:</strong> {{ $sale->waiter->name ?? 'N/A' }}</div>
        <div><strong>Voided at:</strong> {{ now()->format('d-M-Y H:i') }}</div>
    </div>

    <div class="divider"></div>

    <div style="font-weight: bold; margin-bottom: 5px;">CANCELLED ITEMS:</div>

    @foreach($sale->details as $detail)
    <div class="item">
        {{ $detail->quantity }}x {{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="footer">
        ORDER CANCELLED
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
