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
                        @if ($footerSection && $footerSection->image)
                            <img src="{{ asset($footerSection->image) }}"
                                alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->frontend_footer_logo ?? null))
                            <img src="{{ asset($setting->frontend_footer_logo) }}"
                                alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->frontend_logo ?? null))
                            <img src="{{ asset($setting->frontend_logo) }}"
                                alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid w-100">
                        @elseif(!empty($setting->logo ?? null))
                            <img src="{{ asset($setting->logo) }}" alt="{{ $setting->app_name ?? config('app.name') }}"
                                class="img-fluid w-100">
                        @else
                            <img src="{{ asset('website/images/footer_logo.png') }}" alt="{{ config('app.name') }}"
                                class="img-fluid w-100">
                        @endif
                    </a>
                    <p>{{ $setting->footer_about ?? ($footerSection->description ?? cms_setting('footer_description', __('Delicious food delivered to your doorstep.'))) }}
                    </p>
                    <ul>
                        @php
                            // Use main settings first, fallback to CMS settings
                            $facebook =
                                $setting->footer_facebook ??
                                ($socialLinks['social.facebook'] ?? ($socialLinks['facebook'] ?? null));
                            $twitter =
                                $setting->footer_twitter ??
                                ($socialLinks['social.twitter'] ?? ($socialLinks['twitter'] ?? null));
                            $instagram =
                                $setting->footer_instagram ??
                                ($socialLinks['social.instagram'] ?? ($socialLinks['instagram'] ?? null));
                            $youtube =
                                $setting->footer_youtube ??
                                ($socialLinks['social.youtube'] ?? ($socialLinks['youtube'] ?? null));
                            $linkedin = $socialLinks['social.linkedin'] ?? ($socialLinks['linkedin'] ?? null);
                        @endphp
                        @if (!empty($facebook))
                            <li><a class="facebook" href="{{ $facebook }}" target="_blank"><i
                                        class="fab fa-facebook-f"></i></a></li>
                        @endif
                        @if (!empty($twitter))
                            <li><a class="twitter" href="{{ $twitter }}" target="_blank"><i
                                        class="fab fa-twitter"></i></a></li>
                        @endif
                        @if (!empty($instagram))
                            <li><a class="instagram" href="{{ $instagram }}" target="_blank"><i
                                        class="fab fa-instagram"></i></a></li>
                        @endif
                        @if (!empty($linkedin))
                            <li><a class="linkedin" href="{{ $linkedin }}" target="_blank"><i
                                        class="fab fa-linkedin-in"></i></a></li>
                        @endif
                        @if (!empty($youtube))
                            <li><a class="youtube" href="{{ $youtube }}" target="_blank"><i
                                        class="fab fa-youtube"></i></a></li>
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
                            // Use main settings first (address, mobile, email), fallback to CMS
                            $address = $setting->address ?? (cms_contact('address') ?? cms_setting('contact_address'));
                            $phone = $setting->mobile ?? (cms_contact('phone') ?? cms_setting('contact_phone'));
                            $email = $setting->email ?? (cms_contact('email') ?? cms_setting('contact_email'));
                        @endphp
                        <li><a href="javascript:void(0);"><i
                                    class="fas fa-map-marker-alt me-2"></i>{{ $address ?? __('Address not set') }}</a>
                        </li>
                        <li><a href="tel:{{ preg_replace('/[\s\-]/', '', $phone ?? '') }}"><i
                                    class="fas fa-phone-alt me-2"></i>{{ $phone ?? __('Phone not set') }}</a></li>
                        <li><a href="mailto:{{ $email ?? '' }}"><i
                                    class="fas fa-envelope me-2"></i>{{ $email ?? __('Email not set') }}</a></li>
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
                        @if (!empty($setting->copyright_text ?? null))
                            <p>{{ __('Copyright') }} © {{ $setting->app_name ?? config('app.name') }}
                                {{ date('Y') }}. {{ $setting->copyright_text }} Developed by <a
                                    href="https://narzotech.com/"><b>NarzoTech</b></a></p>
                        @else
                            <p>{{ __('Copyright') }} © {{ $setting->app_name ?? config('app.name') }}
                                {{ date('Y') }}. {{ __('All Rights Reserved') }} Developed by <a
                                    href="https://narzotech.com/"><b>NarzoTech</b></a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--==========FOOTER END===========-->
