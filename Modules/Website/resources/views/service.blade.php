@extends('website::layouts.master')

@section('title', 'service - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>Our Services</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">Our Services</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========SERVICE START===========-->
            <section class="service_area pt_95 xs_pt_75">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_1.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Fardel & Spies</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_2.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Fresh Products</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_3.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Great Coffee</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_4.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Vegan Cuisine</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_5.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Skilled Chefs</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_6.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Super Fast Delivery</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_7.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Fresh Products</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_8.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Discount Voucher</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ asset('website/images/service_9.jpg') }}" alt="img" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        <img src="{{ asset('website/images/fress.png') }}" alt="img" class="img-fluid w-100">
                                    </div>
                                    <h2>Fresh Products</h2>
                                    <p>We brew delicious, award-winning beers and fry up the crispiest, juiciest hot
                                        chicken
                                        aroun We serve it with.</p>
                                    <a class="common_btn" href="{{ route('website.service-details', 'sample-service') }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                        </span>
                                        View All Details
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pagination_area mt_60 wow fadeInUp">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="far fa-arrow-left"></i>
                                    </a>
                                </li>
                                <li class="page-item"><a class="page-link active" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </section>
            <!--==========SERVICE END===========-->
@endsection
