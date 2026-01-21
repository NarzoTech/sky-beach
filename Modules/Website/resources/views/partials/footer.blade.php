<!--==========FOOTER START===========-->
<footer class="pt_100 mt_120 xs_mt_100">
    <div class="container">
        <div class="row justify-content-between wow fadeInUp">
            <div class="col-lg-3 col-md-4">
                <div class="footer_info">
                    <a class="footer_logo" href="{{ route('website.index') }}">
                        <img src="{{ asset('website/images/footer_logo.png') }}" alt="CTAKE" class="img-fluid w-100">
                    </a>
                    <p>Cras incident lobotids feudist makes viramas sagittas eu valuta.</p>
                    <ul>
                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>Quick Links</h3>
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
                    <h3>Pages</h3>
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
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="{{ route('website.privacy-policy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('website.terms-condition') }}">{{ __('Terms & Conditions') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-8">
                <div class="footer_contact">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>16/A, Romadan House City Tower New York, United States</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+990123456789">+990 123 456 789</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@ctake.com">info@ctake.com</a>
                        </li>
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
                        <p>Copyright © CTAKE {{ date('Y') }}. All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--==========FOOTER END===========-->
