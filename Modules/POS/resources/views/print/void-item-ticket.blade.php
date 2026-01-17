<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VOID Item - Order #{{ $sale->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            width: {{ $printer->paper_width ?? 80 }}mm;
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
            font-size: 16px;
            padding: 10px 0;
            text-decoration: line-through;
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
            body { width: {{ $printer->paper_width ?? 80 }}mm; }
            @page { margin: 0; size: {{ $printer->paper_width ?? 80 }}mm auto; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">*** VOID ITEM ***</div>
        <div>ORDER #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($sale->table)
        <div>TABLE: {{ $sale->table->name }}</div>
        @endif
    </div>

    <div>
        <strong>Time:</strong> {{ now()->format('d-M-Y H:i') }}
    </div>

    <div class="divider"></div>

    <div class="item">
        <strong>{{ $item->quantity }}x</strong>
        {{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}
    </div>

    @if($item->void_reason)
    <div class="reason">
        <strong>Reason:</strong> {{ $item->void_reason }}
    </div>
    @endif

    <div class="divider"></div>

    <div class="footer">ITEM CANCELLED</div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
