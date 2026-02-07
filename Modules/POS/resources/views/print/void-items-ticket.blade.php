<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VOID Items - Order #{{ $sale->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .item {
            padding: 5px 0;
            text-decoration: line-through;
            color: #666;
        }
        .reason {
            margin-top: 10px;
            padding: 8px;
            background: #fff3cd;
            border: 1px solid #ffc107;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            color: #dc3545;
            font-weight: bold;
        }
        @media print {
            body { width: {{ optional($printer)->paper_width ?? 80 }}mm; }
            @page { margin: 0; size: {{ optional($printer)->paper_width ?? 80 }}mm auto; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">*** VOID ITEMS ***</div>
        <div>ORDER #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($sale->table)
        <div>TABLE: {{ $sale->table->name }}</div>
        @endif
    </div>

    <div>
        <strong>Time:</strong> {{ now()->format('d-M-Y H:i') }}
    </div>

    <div class="divider"></div>

    <div style="font-weight: bold; margin-bottom: 5px;">CANCELLED ITEMS:</div>

    @foreach($items as $item)
    <div class="item">
        {{ $item->quantity }}x {{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}
    </div>
    @endforeach

    @if($items->first()->void_reason)
    <div class="reason">
        <strong>Reason:</strong> {{ $items->first()->void_reason }}
    </div>
    @endif

    <div class="divider"></div>

    <div class="footer">{{ $items->count() }} ITEMS CANCELLED</div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
