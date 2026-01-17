<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Print - {{ $printer->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: {{ $printer->paper_width }}mm;
            padding: 5mm;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .center {
            text-align: center;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
        }
        @media print {
            body {
                width: {{ $printer->paper_width }}mm;
            }
            @page {
                margin: 0;
                size: {{ $printer->paper_width }}mm auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">*** TEST PRINT ***</div>
        <div>{{ $printer->name }}</div>
    </div>

    <div class="row">
        <span>Printer Type:</span>
        <span>{{ ucfirst($printer->type) }}</span>
    </div>
    <div class="row">
        <span>Paper Width:</span>
        <span>{{ $printer->paper_width }}mm</span>
    </div>
    <div class="row">
        <span>Connection:</span>
        <span>{{ ucfirst($printer->connection_type) }}</span>
    </div>

    <div class="divider"></div>

    <div class="center">
        <p>================================</p>
        <p>1234567890</p>
        <p>ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
        <p>abcdefghijklmnopqrstuvwxyz</p>
        <p>================================</p>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>Test Item 1</span>
        <span>$10.00</span>
    </div>
    <div class="row">
        <span>Test Item 2</span>
        <span>$15.50</span>
    </div>
    <div class="row">
        <span>Test Item 3</span>
        <span>$8.75</span>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span><strong>TOTAL:</strong></span>
        <span><strong>$34.25</strong></span>
    </div>

    <div class="footer">
        <p>Print test completed successfully!</p>
        <p>{{ now()->format('d-M-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
