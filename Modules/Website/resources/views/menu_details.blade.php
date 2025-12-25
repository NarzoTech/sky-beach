@extends('website::layouts.master')

@section('title', 'menu_details - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>menu details</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">menu details</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========MENU DETAILS START===========-->
            <section class="menu_details pt_120 xs_pt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-md-8 col-lg-5 wow fadeInLeft">
                            <div class="menu_det_slider_area ">
                                <div class="row slider-for">
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_1.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_2.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_3.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_4.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_5.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ asset('website/images/product_img_6.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>
                                <div class="row slider-nav">
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_1.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_2.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_3.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_4.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_5.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="details_small_img">
                                            <img src="{{ asset('website/images/product_img_6.jpg') }}" alt="slider"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-5 col-md-8 col-lg-7 wow fadeInUp">
                            <div class="menu_det_text">
                                <h2 class="details_title">Chicken Buffalo Wing</h2>
                                <p class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <i class="far fa-star"></i>
                                    <span>Review (20)</span>
                                </p>
                                <p class="price">$10.50 <del>$12.00</del></p>
                                <div class="details_short_description">
                                    <h3>Description</h3>
                                    <p>Pellentesque habitant morbi tristique senectus et netet malesuada
                                        famesPellentesque
                                        habitant morbi tristique senectus.</p>
                                </div>

                                <div class="details_size">
                                    <h5>select size</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="large"
                                            checked>
                                        <label class="form-check-label" for="large">
                                            large <span>+ $350</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault"
                                            id="medium">
                                        <label class="form-check-label" for="medium">
                                            medium <span>+ $250</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="small">
                                        <label class="form-check-label" for="small">
                                            small <span>+ $150</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="details_extra_item">
                                    <h5>select option <span>(optional)</span></h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="coca-cola">
                                        <label class="form-check-label" for="coca-cola">
                                            coca-cola <span>+ $10</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="7up">
                                        <label class="form-check-label" for="7up">
                                            7up <span>+ $15</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="details_quentity">
                                    <div class="quentity_btn_area d-flex flex-wrapa align-items-center">
                                        <div class="quentity_btn">
                                            <button class="btn btn-danger"><i class="fal fa-minus"></i></button>
                                            <input type="text" placeholder="1">
                                            <button class="btn btn-success"><i class="fal fa-plus"></i></button>
                                        </div>
                                        <h3>$320.00</h3>
                                    </div>
                                </div>

                                <div class="details_cart_btn">
                                    <a class="common_btn" href="#">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/cart_icon_1.png') }}" alt="order"
                                                class="img-fluid w-100">
                                        </span>
                                        add to cart
                                    </a>
                                    <a class="common_btn" href="{{ route('website.checkout') }}">
                                        buy now
                                    </a>
                                    <a class="love" href="#"><i class="far fa-heart"></i></a>
                                </div>
                                <ul class="share">
                                    <li>Share with friends:</li>
                                    <li><a href="#"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fab fa-behance" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-8 d-lg-none d-xl-block wow fadeInRight">
                            <div class="">
                                <div class="menu_details_offer">
                                    <p>No featured offers available</p>
                                    <a tabindex="0" data-bs-placement="bottom" data-bs-toggle="popover"
                                        data-bs-trigger="focus" data-bs-custom-class="custom-popover"
                                        data-bs-title="No featured offers available"
                                        data-bs-content="Lorem ipsum dolor sit amet consectetur, adipisicing elit. Voluptatum pariatur deleniti illo, esse eveniet odit laudantium corporis doloribus.">
                                        learn more <i class="far fa-chevron-down"></i>
                                    </a>
                                </div>
                                <div class="menu_details_banner">
                                    <img src="{{ asset('website/images/details_banner_img.png') }}" alt="offer" class="img-fluid w-100">
                                    <div class="text">
                                        <h5>Get Up to 50% Off</h5>
                                        <h3>Burger Combo Pack</h3>
                                        <a href="#">
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
                    <div class="row mt_120 xs_mt_100 wow fadeInUp">
                        <div class="col-12">
                            <div class="menu_det_content_area">
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                            aria-selected="true">Description</button>
                                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-contact" type="button" role="tab"
                                            aria-controls="nav-contact" aria-selected="false">Reviews</button>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                        aria-labelledby="nav-home-tab" tabindex="0">
                                        <div class="menu_det_description">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                industry. Lorem
                                                Ipsum has been the industry's standard dummy text ever since the 1500s,
                                                when an
                                                unknown printer took a galley of type and scrambled it to make a type
                                                specimen
                                                book. It has survived not only five centuries, but also the leap into
                                                electronic
                                                typese
                                                tting, remaining essentially unchanged. It was popularised in the 1960s
                                                with the
                                                release of Letraset sheets containing Lorem Ipsum passages, and more
                                                recently
                                                with
                                                desktop publishing software like Aldus PageMaker including versions of
                                                Lorem
                                                Ipsum.</p>
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                industry. Lorem
                                                Ipsum has been the industry's standard dummy text ever since the 1500s,
                                                when an
                                                unknown printer took a galley of type.</p>

                                            <ul>
                                                <li>One popular belief, Lorem Ipsum is not simply random.</li>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                                <li>To popular belief, Lorem Ipsum is not simply random.</li>
                                                <li>Contrary to popular belief, Lorem Ipsum is not simply random.</li>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                            </ul>
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                industry. Lorem
                                                Ipsum has been the industry's standard dummy text ever since the 1500s,
                                                when an
                                                unknown printer took a galley of type and scrambled it to make a type
                                                specimen
                                                book. It has survived not only five centuries, but also the leap into
                                                electronic
                                                typese
                                                tting, remaining essentially unchanged. It was popularised in the 1960.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                        aria-labelledby="nav-contact-tab" tabindex="0">
                                        <div class="menu_det_review_area">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <h2>(15) Reviews</h2>
                                                    <div class="single_review">
                                                        <div class="img">
                                                            <img src="{{ asset('website/images/client_img_1.png') }}" alt="Reviewer"
                                                                class="img-fluid w-100">
                                                        </div>
                                                        <div class="text">
                                                            <h4>Hasnat Abdullah <span>May 8, 2026</span></h4>
                                                            <span class="rating">
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="far fa-star"></i>
                                                            </span>
                                                            <p>Lorem ipsum is simply free text used by copytyping
                                                                refreshing.
                                                                Neque
                                                                porro est is a rem ipsum qu
                                                                ia qued inventore veritatis et quasi architecto beatae
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="single_review">
                                                        <div class="img">
                                                            <img src="{{ asset('website/images/client_img_2.png') }}" alt="Reviewer"
                                                                class="img-fluid w-100">
                                                        </div>
                                                        <div class="text">
                                                            <h4>Sinthis Mou <span>May 8, 2026</span></h4>
                                                            <span class="rating">
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star-half-alt"></i>
                                                            </span>
                                                            <p>Lorem ipsum is simply free text used by copytyping
                                                                refreshing.
                                                                Neque
                                                                porro est is a rem ipsum qu
                                                                ia qued inventore veritatis et quasi architecto beatae
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="single_review">
                                                        <div class="img">
                                                            <img src="{{ asset('website/images/client_img_2.png') }}" alt="Reviewer"
                                                                class="img-fluid w-100">
                                                        </div>
                                                        <div class="text">
                                                            <h4>Samira Khanom <span>May 8, 2026</span></h4>
                                                            <span class="rating">
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star-half-alt"></i>
                                                                <i class="far fa-star"></i>
                                                            </span>
                                                            <p>Lorem ipsum is simply free text used by copytyping
                                                                refreshing.
                                                                Neque
                                                                porro est is a rem ipsum qu
                                                                ia qued inventore veritatis et quasi architecto beatae
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="pagination_area mt_30">
                                                        <nav aria-label="Page navigation example">
                                                            <ul class="pagination justify-content-center">
                                                                <li class="page-item">
                                                                    <a class="page-link" href="#" aria-label="Previous">
                                                                        <i class="far fa-arrow-left"></i>
                                                                    </a>
                                                                </li>
                                                                <li class="page-item"><a class="page-link active"
                                                                        href="#">1</a>
                                                                </li>
                                                                <li class="page-item"><a class="page-link"
                                                                        href="#">2</a></li>
                                                                <li class="page-item"><a class="page-link"
                                                                        href="#">3</a></li>
                                                                <li class="page-item">
                                                                    <a class="page-link" href="#" aria-label="Next">
                                                                        <i class="far fa-arrow-right"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </nav>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="review_input_area">
                                                        <h2>Write A Review</h2>
                                                        <p>
                                                            Select Your Rating :
                                                            <span>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                                <i class="fas fa-star"></i>
                                                            </span>
                                                        </p>
                                                        <form>
                                                            <div class="review_input_box">
                                                                <label>Name *</label>
                                                                <input type="text" placeholder="Name">
                                                            </div>
                                                            <div class="review_input_box">
                                                                <label>Email *</label>
                                                                <input type="email" placeholder="Email">
                                                            </div>
                                                            <div class="review_input_box">
                                                                <label>Write Review *</label>
                                                                <textarea rows="5"
                                                                    placeholder="Write your review"></textarea>
                                                            </div>
                                                            <button type="submit" class="common_btn">Submit Review
                                                                <span></span></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========MENU DETAILS END===========-->


            <!--==========RELATED MENU START===========-->
            <section class="related_menu pt_105 xs_pt_85">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-xl-5">
                            <div class="section_heading heading_left mb_25">
                                <h2>Related food</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row related_slider">
                        <div class="col-xl-3 wow fadeInUp">
                            <div class="single_menu">
                                <div class="single_menu_img">
                                    <img src="{{ asset('website/images/menu_img_1.jpg') }}" alt="menu" class="img-fluid w-100">
                                    <ul>
                                        <li><a href="#"><i class="far fa-eye"></i></a></li>
                                        <li><a href="#"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="single_menu_text">
                                    <p class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <a class="category" href="#">Chicken</a>
                                    <a class="title" href="{{ route('website.menu-details') }}">Daria Shevtsova</a>
                                    <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                        <h3>$40 <del>$50</del></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <div class="single_menu">
                                <div class="single_menu_img">
                                    <img src="{{ asset('website/images/menu_img_2.jpg') }}" alt="menu" class="img-fluid w-100">
                                    <ul>
                                        <li><a href="#"><i class="far fa-eye"></i></a></li>
                                        <li><a href="#"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="single_menu_text">
                                    <p class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <a class="category" href="#">Biryani</a>
                                    <a class="title" href="{{ route('website.menu-details') }}">Hyderabadi Biryani</a>
                                    <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                        <h3>$30 <del>$45</del></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <div class="single_menu">
                                <div class="single_menu_img">
                                    <img src="{{ asset('website/images/menu_img_3.jpg') }}" alt="menu" class="img-fluid w-100">
                                    <ul>
                                        <li><a href="#"><i class="far fa-eye"></i></a></li>
                                        <li><a href="#"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="single_menu_text">
                                    <p class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <a class="category" href="#">Burger</a>
                                    <a class="title" href="{{ route('website.menu-details') }}">Spicy Burger</a>
                                    <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                        <h3>$59 <del>$65</del></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <div class="single_menu">
                                <div class="single_menu_img">
                                    <img src="{{ asset('website/images/menu_img_4.jpg') }}" alt="menu" class="img-fluid w-100">
                                    <ul>
                                        <li><a href="#"><i class="far fa-eye"></i></a></li>
                                        <li><a href="#"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="single_menu_text">
                                    <p class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <a class="category" href="#">Pizza</a>
                                    <a class="title" href="{{ route('website.menu-details') }}">Mexican Pizza</a>
                                    <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                        <h3>$36 <del>$40</del></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <div class="single_menu">
                                <div class="single_menu_img">
                                    <img src="{{ asset('website/images/menu_img_5.jpg') }}" alt="menu" class="img-fluid w-100">
                                    <ul>
                                        <li><a href="#"><i class="far fa-eye"></i></a></li>
                                        <li><a href="#"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="single_menu_text">
                                    <p class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <a class="category" href="{{ route('website.menu-details') }}">Kabab</a>
                                    <a class="title" href="#">Mozzarella Sticks</a>
                                    <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                        <h3>$20 <del>$30</del></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========RELATED MENU END===========-->
@endsection
