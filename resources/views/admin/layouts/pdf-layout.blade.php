<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        thead {
            display: table-row-group;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif; margin: 20px;">

    <!-- Header -->
    <div style="width: 100%; margin-bottom: 20px;">
        <div style="width: 49%; display: inline-block; vertical-align: top;">
            <img src="{{ asset($setting->logo) }}" alt="Logo" style="width: 80px; height: auto; margin-bottom: 10px;">
        </div>
        <div style="width: 50%; display: inline-block; text-align:right;">
            <p style="margin: 0; font-size: 16px; font-weight: bold; color: #003366;">
                {{ $setting->app_name }}
            </p>
            <p style="margin: 0; font-size: 12px; color: #333;">
                {{ $setting->address }}<br>
                Tel: {{ $setting->mobile }}<br>
                <a href="mailto:{{ $setting->email }}"
                    style="color: #003366; text-decoration: none;">{{ $setting->email }}</a>
                |
            </p>
        </div>
    </div>

    <!-- Section Title -->
    <h2
        style="color: #003366; font-size: 18px; text-align: center; margin-bottom: 20px; border-top: 2px solid #003366; padding-top: 30px;">
        @yield('title')
    </h2>
    @yield('content')

    <script>
        window.print();
    </script>
</body>

</html>
