@extends('website::layouts.master')

@section('title', 'Privacy Policy - CTAKE')

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>Privacy Policy</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.privacy-policy') }}">Privacy Policy</a></li>
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
                            <h4>Information We Collect</h4>
                            <p>We may share your information with trusted service providers to facilitate our
                                services. We
                                ensure that these partners maintain the same level of data protection and
                                security as us. Additionally, we may disclose information as required by law or to
                                protect
                                our rights and interests. We may share your information with trusted service
                                providers to facilitate our services.</p>

                            <h4>How We Use Your Information</h4>
                            <p>We may share your information with trusted service providers to facilitate our
                                services. We
                                ensure that these partners maintain the same level of data protection and
                                security as us. Additionally, we may disclose information as required.</p>

                            <h4>Sharing Your Information</h4>
                            <p>We may share your information with trusted service providers to facilitate our
                                services. We
                                ensure that these partners maintain the same level of data protection and
                                security as us. Additionally, we may disclose information as required by law or to
                                protect
                                our rights and interests. We may share your information with trusted service
                                providers to facilitate our services.</p>

                            <ul>
                                <li>We implement industry-standard security measures to protect your information.</li>
                                <li>Opt out of marketing communications, and request the deletion of your data.</li>
                                <li>Please contact us to exercise these rights.</li>
                                <li>If you have any questions or concerns about our Privacy Policy.</li>
                                <li>Cancellation and Rescheduling</li>
                                <li>Feedback and Reviews</li>
                            </ul>

                            <h4>Your Choices</h4>
                            <p>We implement industry-standard security measures to protect your information from
                                unauthorized access, alteration, or disclosure. However, please be aware that no
                                data transmission over the internet is entirely secure.</p>

                            <h4>Security Measures</h4>
                            <p>You have the right to update or correct your information, opt out of marketing
                                communications, and request the deletion of your data. Please contact us to exercise
                                these rights.</p>

                            <h4>Changes to This Policy</h4>
                            <p>We may update our Privacy Policy as our practices evolve. We will notify you of any
                                significant changes and provide the updated policy on our website. You have the
                                right to update or correct your information.</p>

                            <h4>Contact Us</h4>
                            <p>If you have any questions or concerns about our Privacy Policy or data practices,
                                please contact us at:</p>
                            <a href="mailto:support@ctake.com">Email: support@ctake.com</a>
                            <p>Location: 800S, Salt Lake City, USA</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========PRIVACY POLICY END===========-->
@endsection
