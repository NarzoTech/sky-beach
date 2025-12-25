@extends('website::layouts.master')

@section('title', 'menu - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>menu style 03</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">menu style 03</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========MENU PAGE START===========-->
            <section class="menu_grid_view mt_120 xs_mt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 order-2 wow fadeInLeft">
                            <div class="menu_sidebar ">
                                <div class="sidebar_wizard sidebar_search">
                                    <h2>Search</h2>
                                    <form>
                                        <input type="text" placeholder="Search here...">
                                        <button type="submit"><i class="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="sidebar_wizard sidebar_price_ranger mt_25">
                                    <h2>Pricing Filter</h2>
                                    <div class="price_ranger">
                                        <input type="hidden" id="slider_range" class="flat-slider" />
                                    </div>
                                </div>
                                <div class="sidebar_wizard sidebar_category mt_25">
                                    <h2>Categories</h2>
                                    <ul>
                                        <li><a href="menu_03.html">Food <span>(04)</span></a></li>
                                        <li><a href="menu_03.html">Drinks <span>(02)</span></a></li>
                                        <li><a href="menu_03.html">Burger <span>(09)</span></a></li>
                                        <li><a href="menu_03.html">Chicken <span>(14)</span></a></li>
                                        <li><a href="menu_03.html">Pizza <span>(05)</span></a></li>
                                        <li><a href="menu_03.html">Combo <span>(07)</span></a></li>
                                    </ul>
                                </div>
                                <div class="sidebar_wizard sidebar_tags mt_25">
                                    <h2>Categories</h2>
                                    <ul>
                                        <li><a href="#">Food</a></li>
                                        <li><a href="#">Drinks</a></li>
                                        <li><a href="#">Burger</a></li>
                                        <li><a href="#">Chicken</a></li>
                                        <li><a href="#">Pizza</a></li>
                                        <li><a href="#">Combo</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-8 order-lg-2">
                            <div class="row">
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                                            <a class="category" href="#">Kabab</a>
                                            <a class="title" href="{{ route('website.menu-details') }}">Mozzarella Sticks</a>
                                            <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                                <h3>$20 <del>$30</del></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
                                    <div class="single_menu">
                                        <div class="single_menu_img">
                                            <img src="{{ asset('website/images/menu_img_6.jpg') }}" alt="menu" class="img-fluid w-100">
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
                                            <a class="category" href="#">Fry</a>
                                            <a class="title" href="{{ route('website.menu-details') }}">Popcorn Chicken</a>
                                            <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                                <h3>$36 <del>$45</del></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
                                    <div class="single_menu">
                                        <div class="single_menu_img">
                                            <img src="{{ asset('website/images/menu_img_7.jpg') }}" alt="menu" class="img-fluid w-100">
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
                                            <a class="category" href="#">Sandwich</a>
                                            <a class="title" href="{{ route('website.menu-details') }}">Mexican Pizza</a>
                                            <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                                <h3>$49 <del>$55</del></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
                                    <div class="single_menu">
                                        <div class="single_menu_img">
                                            <img src="{{ asset('website/images/menu_img_8.jpg') }}" alt="menu" class="img-fluid w-100">
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
                                            <a class="category" href="#">Sandwich</a>
                                            <a class="title" href="{{ route('website.menu-details') }}">Onion Rings</a>
                                            <p class="descrption">Homemade pizza crust, pizza sauce</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="{{ route('website.menu-details') }}">buy now</a>
                                                <h3>$59 <del>$69</del></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
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
                            </div>
                            <div class="pagination_area mt_35 xs_mb_60 wow fadeInUp">
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
                    </div>
                </div>
            </section>
            <!--==========MENU PAGE END===========-->
@endsection
