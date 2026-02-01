@extends('website::layouts.master')

@section('title', __('Page Not Found') . ' - ' . config('app.name'))

@section('content')
    <!--==========ERROR START===========-->
    <section class="error_page mt_120 xs_mt_100">
        <div class="container">
            <div class="row wow fadeInUp">
                <div class="col-xxl-5 col-md-9 col-lg-7 col-xl-6 m-auto">
                    <div class="error_text">
                        <div class="error_img">
                            <img src="{{ asset('website/images/error_img.png') }}" alt="{{ __('Error') }}" class="img-fluid w-100">
                        </div>
                        <h2>{{ __('Oops! Nothing Was Found') }}</h2>
                        <p>{{ __('Something went wrong. Looks like this page doesn\'t exist anymore.') }}</p>
                        <a class="common_btn" href="{{ route('website.index') }}">{{ __('Go Back Home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========ERROR END===========-->
@endsection
