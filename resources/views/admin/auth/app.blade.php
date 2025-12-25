<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed layout-compact layout-navbar-fixed" dir="ltr"
    data-theme="theme-default" data-style="light" data-assets-path="{{ asset('backend/assets') }}/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    @yield('title')
    <link rel="icon" href="{{ asset($setting->favicon) }}">

    <link rel="stylesheet" href="{{ asset('backend/assets/css/page-auth.css') }}">
    @include('admin.layouts.styles')

    <style>
        .template-customizer-open-btn {
            display: none !important;
        }
    </style>
</head>

<body>

    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="" class="app-brand auth-cover-brand gap-2">
            <span class="app-brand-logo demo">

                <img src="{{ asset($setting->logo) }}" alt="{{ asset($setting->app_name) }}">

            </span>
        </a>
        <!-- /Logo -->
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img src="{{ asset($setting->login ?? 'backend/assets/img/illustrations/boy-with-rocket-light.png') }}"
                        class="img-fluid" alt="Login image" width="700">
                </div>
            </div>
            <!-- /Left Text -->

            @yield('content')
        </div>
    </div>

    <!-- / Content -->



    @include('admin.layouts.javascripts')


    <!-- Page JS -->
    <script src="{{ asset('backend/assets/js/pages-auth.js') }}"></script>

</body>

</html>
