@extends('website::layouts.master')

@section('title', 'faq - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>FAQ</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">FAQ</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========FAQ'S START===========-->
            <section class="faq_area pt_95 xs_pt_70">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-xl-6 wow fadeInLeft">
                            <div class="accordion faq_accordion" id="accordionPanelsStayOpenExample">
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
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingSix">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSix"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseSix">
                                            Does your pizza contain peanuts or peanut oil?
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseSix" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingSix">
                                        <div class="accordion-body">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry
                                                Lorem Ipsum has been the industry's standard dummy text ever since the
                                                1500s when an unknown took a galley.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 my-auto wow fadeInRight">
                            <div class="faq_img">
                                <img src="{{ asset('website/images/faq_1.png') }}" alt="faq" class="img-fluid w-100">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq_contact mt_120 xs_mt_100 pt_120 xs_pt_80 pb_120 xs_pb_80">
                    <div class="container">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-xl-5 col-lg-6 wow fadeInLeft">
                                <div class="faq_contact_img">
                                    <img src="{{ asset('website/images/faq_contact_img.png') }}" alt="FAQ's" class="img-fluid w-100">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 wow fadeInRight">
                                <div class="faq_contact_form contact_form">
                                    <h2>Have Any question</h2>
                                    <form action="#">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" placeholder="Your Name">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" placeholder="Your Email">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" placeholder="Phone Number">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" placeholder="Subject">
                                            </div>
                                            <div class="col-md-12">
                                                <textarea rows="7" placeholder="Write Message..."></textarea>
                                                <button class="common_btn">Submit now</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========FAQ'S END===========-->
@endsection
