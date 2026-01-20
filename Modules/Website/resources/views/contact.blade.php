@extends('website::layouts.master')

@section('title', 'Contact Us - CTAKE')

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>Contact Us</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.contact') }}">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CONTACT START===========-->
        <section class="contact_us pt_120 xs_pt_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8 wow fadeInLeft">
                        <div class="contact_img">
                            <img src="{{ asset('website/images/contact_img.jpg') }}" alt="contact" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight">
                        <div class="contact_form">
                            <h2>Get In Touch</h2>
                            <form action="{{ route('website.contact.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="name" placeholder="Your Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" placeholder="Your Email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="phone" placeholder="Phone Number">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="subject" placeholder="Subject" required>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea rows="7" name="message" placeholder="Write Message..." required></textarea>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="newsletter" value="1"
                                                id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Subscribe to our newsletter for updates about our services.
                                            </label>
                                        </div>
                                        <button type="submit" class="common_btn">Submit Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row mt_95 xs_mt_75">
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/location_2.png') }}" alt="location" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <p>16/A, Romadan House City Tower New York, United States</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/call_icon_3.png') }}" alt="call" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <a href="tel:+990123456789">+990 123 456 789</a>
                                <a href="tel:+990456123789">+990 456 123 789</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/mail_icon.png') }}" alt="mail" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <a href="mailto:info@ctake.com">info@ctake.com</a>
                                <a href="mailto:support@ctake.com">support@ctake.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contact_map mt_120 xs_mt_100 wow fadeInUp">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3104.8776746534986!2d-77.027541687759!3d38.903912546200644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7b7931d95b707%3A0x16e85cf5a8a5fdce!2sMarriott%20Marquis%20Washington%2C%20DC!5e0!3m2!1sen!2sbd!4v1700767199965!5m2!1sen!2sbd"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
        <!--==========CONTACT END===========-->

    </div>
</div>
@endsection
