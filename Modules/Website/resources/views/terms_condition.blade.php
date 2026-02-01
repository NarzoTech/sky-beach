@extends('website::layouts.master')

@php
    $legalPage = cms_legal_page('terms-conditions');
@endphp

@section('title', ($legalPage->title ?? __('Terms & Conditions')) . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $legalPage->title ?? __('Terms & Conditions') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.terms-condition') }}">{{ __('Terms & Conditions') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========TERMS & CONDITION START===========-->
        <section class="terms_condition mt_120 xs_mt_100">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="privacy_policy_text">
                            @if($legalPage && $legalPage->content)
                                {!! $legalPage->content !!}
                            @else
                                <h4>{{ __('Acceptance of Terms') }}</h4>
                                <p>{{ __('By accessing and using our website and services, you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use our services.') }}</p>

                                <h4>{{ __('Use of Services') }}</h4>
                                <p>{{ __('Our services are provided for your personal, non-commercial use. You agree to use our services only for lawful purposes and in accordance with these Terms. You are responsible for maintaining the confidentiality of your account information.') }}</p>

                                <h4>{{ __('Orders and Payments') }}</h4>
                                <p>{{ __('All orders are subject to availability and confirmation of the order price. We reserve the right to refuse any order you place with us. Payment must be received prior to the acceptance of an order.') }}</p>

                                <ul>
                                    <li>{{ __('All prices are subject to change without notice.') }}</li>
                                    <li>{{ __('We accept major credit cards and other specified payment methods.') }}</li>
                                    <li>{{ __('You are responsible for any applicable taxes.') }}</li>
                                    <li>{{ __('Refunds are processed according to our refund policy.') }}</li>
                                    <li>{{ __('Cancellation and Rescheduling policies apply.') }}</li>
                                </ul>

                                <h4>{{ __('Intellectual Property') }}</h4>
                                <p>{{ __('All content on this website, including but not limited to text, graphics, logos, images, and software, is the property of') }} {{ config('app.name') }} {{ __('and is protected by intellectual property laws.') }}</p>

                                <h4>{{ __('Limitation of Liability') }}</h4>
                                <p>{{ __('We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use our services.') }}</p>

                                <h4>{{ __('Changes to Terms') }}</h4>
                                <p>{{ __('We reserve the right to modify these Terms at any time. Changes will be effective immediately upon posting on the website. Your continued use of our services constitutes acceptance of the modified terms.') }}</p>

                                <h4>{{ __('Contact Us') }}</h4>
                                <p>{{ __('If you have any questions about these Terms and Conditions, please contact us at:') }}</p>
                                @php $email = $setting->email ?? cms_contact('email'); @endphp
                                <a href="mailto:{{ $email }}">{{ __('Email') }}: {{ $email ?? __('Not set') }}</a>
                                <p>{{ __('Location') }}: {{ $setting->address ?? cms_contact('address') ?? __('Address not set') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========TERMS & CONDITION END===========-->
@endsection
