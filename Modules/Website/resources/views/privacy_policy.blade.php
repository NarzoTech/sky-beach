@extends('website::layouts.master')

@php
    $legalPage = cms_legal_page('privacy-policy');
@endphp

@section('title', ($legalPage->title ?? __('Privacy Policy')) . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $legalPage->title ?? __('Privacy Policy') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.privacy-policy') }}">{{ __('Privacy Policy') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========PRIVACY POLICY START===========-->
        <section class="privacy_policy mt_120 xs_mt_100">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="privacy_policy_text">
                            @if($legalPage && $legalPage->content)
                                {!! $legalPage->content !!}
                            @else
                                <h4>{{ __('Information We Collect') }}</h4>
                                <p>{{ __('We may share your information with trusted service providers to facilitate our services. We ensure that these partners maintain the same level of data protection and security as us. Additionally, we may disclose information as required by law or to protect our rights and interests.') }}</p>

                                <h4>{{ __('How We Use Your Information') }}</h4>
                                <p>{{ __('We may share your information with trusted service providers to facilitate our services. We ensure that these partners maintain the same level of data protection and security as us. Additionally, we may disclose information as required.') }}</p>

                                <h4>{{ __('Sharing Your Information') }}</h4>
                                <p>{{ __('We may share your information with trusted service providers to facilitate our services. We ensure that these partners maintain the same level of data protection and security as us. Additionally, we may disclose information as required by law or to protect our rights and interests.') }}</p>

                                <ul>
                                    <li>{{ __('We implement industry-standard security measures to protect your information.') }}</li>
                                    <li>{{ __('Opt out of marketing communications, and request the deletion of your data.') }}</li>
                                    <li>{{ __('Please contact us to exercise these rights.') }}</li>
                                    <li>{{ __('If you have any questions or concerns about our Privacy Policy.') }}</li>
                                </ul>

                                <h4>{{ __('Your Choices') }}</h4>
                                <p>{{ __('We implement industry-standard security measures to protect your information from unauthorized access, alteration, or disclosure. However, please be aware that no data transmission over the internet is entirely secure.') }}</p>

                                <h4>{{ __('Security Measures') }}</h4>
                                <p>{{ __('You have the right to update or correct your information, opt out of marketing communications, and request the deletion of your data. Please contact us to exercise these rights.') }}</p>

                                <h4>{{ __('Changes to This Policy') }}</h4>
                                <p>{{ __('We may update our Privacy Policy as our practices evolve. We will notify you of any significant changes and provide the updated policy on our website.') }}</p>

                                <h4>{{ __('Contact Us') }}</h4>
                                <p>{{ __('If you have any questions or concerns about our Privacy Policy or data practices, please contact us at:') }}</p>
                                @php $email = $setting->email ?? cms_contact('email'); @endphp
                                <a href="mailto:{{ $email }}">{{ __('Email') }}: {{ $email ?? __('Not set') }}</a>
                                <p>{{ __('Location') }}: {{ $setting->address ?? cms_contact('address') ?? __('Address not set') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========PRIVACY POLICY END===========-->
@endsection
