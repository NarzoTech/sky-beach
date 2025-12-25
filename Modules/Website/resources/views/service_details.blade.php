@extends('website::layouts.master')

@section('title', 'service_details - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>Services Details</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">Services Details</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========SERVICE DETAILS START===========-->
            <section class="service_details pt_120 xs_pt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 wow fadeInLeft">
                            <div class="service_details_img">
                                <img src="{{ asset('website/images/service_details.jpg') }}" alt="blog details" class="img-fluid w-100">
                            </div>
                            <div class="service_details_text">
                                <h2>Fresh Products</h2>
                                <p>Al contrario del pensamiento popular, el texto de Lorem Ipsum no es simplemente texto
                                    aleatorio. Tiene sus raices en una pieza clÂ´sica de la literatura del Latin, que
                                    data del
                                    aÃ±o 45 antes de Cristo, haciendo que este adquiera mas de 2000 aÃ±os de antiguedad.
                                    Richard
                                    McClintock, un profesor de Latin de la Universidad de Hampden-Sydney en Virginia,
                                    encontrÃ³
                                    una de las palabras mÃ¡s oscuras de la lengua del latÃ­n, "consecteur",Al contrario
                                    del
                                    pensamiento popular.</p>
                                <p>Al contrario del pensamiento popular, el texto de Lorem Ipsum no es simplemente texto
                                    aleatorio. Tiene sus raices en una pieza clÂ´sica de la literatura del Latin, que
                                    data del
                                    aÃ±o 45 antes de Cristo, haciendo que este adquiera mas de 2000 aÃ±os de antiguedad.
                                    Richard
                                    McClintock.</p>
                                <div class="service_quot d-flex flex-wrap">
                                    <p>Lorem an unknown printer took a galley of type and scrambled it to make specimen
                                        book
                                        It hasey survived not only five centuries, but also the leap.</p>
                                    <h5>Miles Tone</h5>
                                </div>
                                <p>Al contrario del pensamiento popular, el texto de Lorem Ipsum no es simplemente texto
                                    aleatorio. Tiene sus raices en una pieza clÂ´sica de la literatura del Latin, que
                                    data del
                                    aÃ±o 45 antes de Cristo, haciendo que este adquiera mas de 2000 aÃ±os de antiguedad.
                                    Richard
                                    McClintock.</p>
                            </div>
                            <div class="accordion faq_accordion service_accordion" id="accordionPanelsStayOpenExample">
                                <h3>Frequently Asked Questions</h3>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                            aria-controls="panelsStayOpen-collapseOne">
                                            How Does This Work?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="panelsStayOpen-headingOne">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                            Does your pizza contain peanuts or peanut oil?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingTwo">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                            Are your doughs vegan or vegetarian friendly?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingThree">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                            Does your pepperoni contain gluten?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingFour">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                            Are your doughs vegan or vegetarian friendly?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingFive">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-8 wow fadeInRight">
                            <div class="blog_sidebar ">
                                <div class="sidebar_wizard sidebar_search">
                                    <h2>Search</h2>
                                    <form>
                                        <input type="text" placeholder="Search here...">
                                        <button type="submit"><i class="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="sidebar_wizard service_category mt_25">
                                    <h2>Services Categories</h2>
                                    <div class="row">
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_1.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Discount Voucher</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_2.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Fresh Products</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_3.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Great Coffee</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_4.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Discount Voucher</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_5.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Super Fast Delivery</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-6">
                                            <div class="service_category_img">
                                                <img src="{{ asset('website/images/service_catg_6.jpg') }}" alt="food"
                                                    class="img-fluid w-100">
                                                <div class="service_category_text">
                                                    <a href="{{ route('website.service') }}">Skilled Chefs</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sidebar_banner menu_details_banner mt_25">
                                    <img src="{{ asset('website/images/offer_bg.jpg') }}" alt="offer" class="img-fluid w-100">
                                    <div class="text">
                                        <h5>Get Up to 50% Off</h5>
                                        <h3>Burger Combo Pack</h3>
                                        <a href="{{ route('website.menu-details') }}">
                                            <span><img src="{{ asset('website/images/cart_icon_2.png') }}" alt="cart"
                                                    class="img-fluid w-100"></span>
                                            shop now
                                            <i class="far fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========SERVICE DETAILS END===========-->
@endsection
