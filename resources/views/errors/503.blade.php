<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Maintenance Mode') }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('website/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        .error_text {
            text-align: center;
            padding: 40px;
        }
        .error_text h2 {
            color: #fff;
            margin-bottom: 15px;
        }
        .error_text p {
            color: #ccc;
        }
    </style>
</head>
<body>
    <section class="error_page">
        <div class="container">
            <div class="row">
                <div class="col-xxl-5 col-md-9 col-lg-7 col-xl-6 m-auto">
                    <div class="error_text">
                        <div class="error_img mb-4">
                            <img src="{{ asset('website/images/error_img.png') }}" alt="{{ __('Maintenance') }}" class="img-fluid" style="max-width: 300px;">
                        </div>
                        <h2>{{ __('We\'ll Be Back Soon!') }}</h2>
                        <p>{{ __('We are currently performing scheduled maintenance. Please check back shortly.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
