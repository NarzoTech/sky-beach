<!doctype html>
<html lang="en">


<head>
    <title>{{ $setting->app_name }}</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

    <style type="text/css">
        :root {
            --width: 100%;
            --height: 24.8749mm;
        }

        @media print {
            .label {
                box-sizing: border-box !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
                width: var(--width);
                height: var(--height);
                border: none !important;
                border: 1px solid black !important;
                border-radius: 10px;
            }
        }

        *,
        *::before,
        *::after {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'Share-Regular';
            src: url('/fonts/Share-Regular.ttf');
        }

        @page {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
            size: var(--width) var(--height);
        }

        body {
            font-family: "Share-Regular";
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .label {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
            width: var(--width);
            height: var(--height);
            text-align: center;
            border: 1px solid;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .label-list {
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            align-items: center;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .text-font {
            font-size: 12px;
        }

        .text-font-2 {
            font-size: 13px;
        }

        .text-bold {
            font-weight: bold
        }
    </style>
</head>

<body>

    <div class="label-list" style="page-break-after: always">
        @foreach ($codes as $code)
            @php
                $product = $products->where('barcode', $code['code'])->first();
            @endphp
            <div class="label">
                <span class="text-font" style="font-weight: bold">
                    @if (in_array('shopname', $action))
                        {{ $setting->app_name }}
                    @endif
                </span>

                <span class="text-font" style="font-weight: bold">
                    @if (in_array('productname', $action))
                        {{ $product->name }}
                    @endif
                </span>
                <span class="text-font" style="font-weight: bold">

                </span>

                <span class="text-font" style="">
                    @if (in_array('category', $action))
                        {{ $product->category?->name }}
                    @endif
                </span>
                {!! $code['qrcode'] !!}
                <span class="text-font" style="margin-bottom: -1px">
                    @if (in_array('sku', $action))
                        {{ $product->sku }}
                    @endif
                </span>
                <span class="text-font" style="font-weight:bold;">
                    @if (in_array('selling_price', $action))
                        Price {{ $product->current_price }} TK
                    @endif
                </span>
            </div>
        @endforeach
    </div>
    <script>
        window.print()
    </script>
</body>

</html>
