<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sky Beach - Food & Restaurant')</title>
    <link rel="icon" type="image/png" href="{{ !empty($setting->frontend_favicon ?? null) ? asset($setting->frontend_favicon) : (!empty($setting->favicon ?? null) ? asset($setting->favicon) : asset('website/images/favicon.png')) }}">
    
    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('website/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/ranger_slider.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/scroll_button.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/custom_spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/colorfulTab.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/responsive.css') }}">

    @stack('styles')
</head>

<body>
    {{-- Header Navigation (Outside smooth-wrapper) --}}
    @include('website::partials.header')

    {{-- Smooth Scroll Wrapper --}}
    <div id="smooth-wrapper">
        <div id="smooth-content">

            {{-- Main Content --}}
            @yield('content')

            {{-- Footer (Inside smooth-content) --}}
            @include('website::partials.footer')

        </div>
    </div>

    {{-- Scroll Button (Outside smooth-wrapper) --}}
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    {{-- JavaScript Files --}}
    <script src="{{ asset('website/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('website/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('website/js/Font-Awesome.js') }}"></script>
    <script src="{{ asset('website/js/slick.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.countup.min.js') }}"></script>
    <script src="{{ asset('website/js/scroll_button.js') }}"></script>
    <script src="{{ asset('website/js/ranger_jquery-ui.min.js') }}"></script>
    <script src="{{ asset('website/js/ranger_slider.js') }}"></script>
    <script src="{{ asset('website/js/select2.min.js') }}"></script>
    <script src="{{ asset('website/js/wow.min.js') }}"></script>
    <script src="{{ asset('website/js/colorfulTab.min.js') }}"></script>
    <script src="{{ asset('website/js/gsap.min.js') }}"></script>
    <script src="{{ asset('website/js/ScrollSmoother.min.js') }}"></script>
    <script src="{{ asset('website/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('website/js/script.js') }}"></script>
    <script src="{{ asset('website/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('website/js/venobox.min.js') }}"></script>
    <script>
        // Initialize Venobox for video lightbox
        document.addEventListener('DOMContentLoaded', function() {
            new VenoBox({
                selector: '.venobox',
                numeration: true,
                infinigall: true,
                share: false,
                spinner: 'rotating-plane'
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
