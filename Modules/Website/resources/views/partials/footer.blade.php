@php
    $footerSection = site_section('footer', 'global');
    $socialLinks = cms_social();
@endphp
<!--==========FOOTER START===========-->
<footer class="pt_100 mt_120 xs_mt_100">
    <div class="container">
        <div class="row justify-content-between wow fadeInUp">
            <div class="col-lg-3 col-md-4">
                <div class="footer_info">
                    <a class="footer_logo" href="{{ route('website.index') }}">
                        @if($footerSection && $footerSection->image)
                            <img src="{{ asset($footerSection->image) }}" alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->frontend_footer_logo ?? null))
                            <img src="{{ asset($setting->frontend_footer_logo) }}" alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->frontend_logo ?? null))
                            <img src="{{ asset($setting->frontend_logo) }}" alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->logo ?? null))
                            <img src="{{ asset($setting->logo) }}" alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @else
                            <img src="{{ asset('website/images/footer_logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid w-100">
                        @endif
                    </a>
                    <p>{{ $footerSection->description ?? cms_setting('footer_description', __('Delicious food delivered to your doorstep.')) }}</p>
                    <ul>
                        @if($socialLinks)
                            @if(!empty($socialLinks['social.facebook'] ?? $socialLinks['facebook'] ?? null))
                                <li><a class="facebook" href="{{ $socialLinks['social.facebook'] ?? $socialLinks['facebook'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                            @endif
                            @if(!empty($socialLinks['social.twitter'] ?? $socialLinks['twitter'] ?? null))
                                <li><a class="twitter" href="{{ $socialLinks['social.twitter'] ?? $socialLinks['twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                            @endif
                            @if(!empty($socialLinks['social.instagram'] ?? $socialLinks['instagram'] ?? null))
                                <li><a class="instagram" href="{{ $socialLinks['social.instagram'] ?? $socialLinks['instagram'] }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                            @endif
                            @if(!empty($socialLinks['social.linkedin'] ?? $socialLinks['linkedin'] ?? null))
                                <li><a class="linkedin" href="{{ $socialLinks['social.linkedin'] ?? $socialLinks['linkedin'] }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                            @endif
                            @if(!empty($socialLinks['social.youtube'] ?? $socialLinks['youtube'] ?? null))
                                <li><a class="youtube" href="{{ $socialLinks['social.youtube'] ?? $socialLinks['youtube'] }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            @endif
                        @else
                            <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>{{ __('Quick Links') }}</h3>
                    <ul>
                        <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('website.menu') }}">{{ __('Menu') }}</a></li>
                        <li><a href="{{ route('website.service') }}">{{ __('Services') }}</a></li>
                        <li><a href="{{ route('website.about') }}">{{ __('About') }}</a></li>
                        <li><a href="{{ route('website.contact') }}">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>{{ __('Pages') }}</h3>
                    <ul>
                        <li><a href="{{ route('website.blogs') }}">{{ __('Blogs') }}</a></li>
                        <li><a href="{{ route('website.chefs') }}">{{ __('Chefs') }}</a></li>
                        <li><a href="{{ route('website.faq') }}">{{ __('FAQs') }}</a></li>
                        <li><a href="{{ route('website.reservation.index') }}">{{ __('Reservation') }}</a></li>
                        <li><a href="{{ route('website.catering.index') }}">{{ __('Catering') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>{{ __('Legal') }}</h3>
                    <ul>
                        <li><a href="{{ route('website.privacy-policy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('website.terms-condition') }}">{{ __('Terms & Conditions') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-8">
                <div class="footer_link footer_contact">
                    <h3>{{ __('Contact Us') }}</h3>
                    <ul>
                        @php
                            $address = cms_contact('address') ?? cms_setting('contact_address');
                            $phone = cms_contact('phone') ?? cms_setting('contact_phone');
                            $email = cms_contact('email') ?? cms_setting('contact_email');
                        @endphp
                        <li><a href="javascript:void(0);"><i class="fas fa-map-marker-alt me-2"></i>{{ $address ?? __('Address not set') }}</a></li>
                        <li><a href="tel:{{ preg_replace('/[\s\-]/', '', $phone ?? '') }}"><i class="fas fa-phone-alt me-2"></i>{{ $phone ?? __('Phone not set') }}</a></li>
                        <li><a href="mailto:{{ $email ?? '' }}"><i class="fas fa-envelope me-2"></i>{{ $email ?? __('Email not set') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_copyright_area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer_copyright">
                        <p>{{ __('Copyright') }} © {{ config('app.name') }} {{ date('Y') }}. {{ __('All Rights Reserved') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--==========FOOTER END===========-->
