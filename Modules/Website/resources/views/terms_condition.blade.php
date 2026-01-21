@extends('website::layouts.master')

@section('title', 'Terms & Conditions - CTAKE')

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>Terms & Conditions</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.terms-condition') }}">Terms & Conditions</a></li>
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
                            <h4>Acceptance of Terms</h4>
                            <p>By accessing and using our website and services, you agree to be bound by these
                                Terms and Conditions. If you do not agree with any part of these terms, please
                                do not use our services.</p>

                            <h4>Use of Services</h4>
                            <p>Our services are provided for your personal, non-commercial use. You agree to use
                                our services only for lawful purposes and in accordance with these Terms. You are
                                responsible for maintaining the confidentiality of your account information.</p>

                            <h4>Orders and Payments</h4>
                            <p>All orders are subject to availability and confirmation of the order price. We reserve
                                the right to refuse any order you place with us. Payment must be received prior to
                                the acceptance of an order.</p>

                            <ul>
                                <li>All prices are subject to change without notice.</li>
                                <li>We accept major credit cards and other specified payment methods.</li>
                                <li>You are responsible for any applicable taxes.</li>
                                <li>Refunds are processed according to our refund policy.</li>
                                <li>Cancellation and Rescheduling policies apply.</li>
                                <li>Feedback and Reviews are welcome.</li>
                            </ul>

                            <h4>Intellectual Property</h4>
                            <p>All content on this website, including but not limited to text, graphics, logos,
                                images, and software, is the property of CTAKE and is protected by intellectual
                                property laws.</p>

                            <h4>Limitation of Liability</h4>
                            <p>We shall not be liable for any indirect, incidental, special, consequential, or
                                punitive damages resulting from your use of or inability to use our services.</p>

                            <h4>Changes to Terms</h4>
                            <p>We reserve the right to modify these Terms at any time. Changes will be effective
                                immediately upon posting on the website. Your continued use of our services
                                constitutes acceptance of the modified terms.</p>

                            <h4>Contact Us</h4>
                            <p>If you have any questions about these Terms and Conditions, please contact us at:</p>
                            <a href="mailto:support@ctake.com">Email: support@ctake.com</a>
                            <p>Location: 800S, Salt Lake City, USA</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========TERMS & CONDITION END===========-->
@endsection
