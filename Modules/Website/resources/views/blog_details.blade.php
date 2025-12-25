@extends('website::layouts.master')

@section('title', 'blog_details - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>blog details</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">blog details</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========BLOG DETAILS START===========-->
            <section class="blog_details mt_120 xs_mt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 wow fadeInLeft">
                            <div class="blog_details_img">
                                <img src="{{ asset('website/images/blog_details_img.jpg') }}" alt="blog details"
                                    class="img-fluid w-100">
                            </div>
                            <div class="blog_details_header">
                                <ul class="left_info">
                                    <li><span>Snacks</span></li>
                                    <li><i class="far fa-user-circle"></i> Admin</li>
                                    <li><i class="far fa-calendar-alt"></i>May 06, 2026</li>
                                </ul>
                                <ul class="right_info">
                                    <li><i class="far fa-comment"></i> 05</li>
                                    <li><i class="far fa-heart"></i> 20</li>
                                    <li><i class="far fa-share-alt"></i> 24</li>
                                </ul>
                            </div>
                            <div class="blog_details_text">
                                <h2>PAIRING WINE WITH INDIAN FOOD: TIPS FROM A SOMMELIER</h2>
                                <p>Pizzhen an unknown printer took a galley of type and scrambled it to make a type
                                    specimen
                                    bookIt hasey survived not only five centuries, but also the leap into electronic
                                    typesetting, remaining essentially unch anged. It was popularised in tf Letraset
                                    sheets
                                    containing.Pizzhen an unknown printer took a galley of typ survived not only five
                                    centuries,
                                    but also the leap into electronic typesetting, remaining essentially unch anged.</p>

                                <p>Pizzhen an unknown printer took a galley of type and scrambled it to make a type
                                    specimen
                                    bookIt hasey survived not only five centuries, but also the leap into electronic
                                    typesetting, remaining essentially </p>

                                <div class="quot_text">
                                    <p>Lorem an unknown printer took a galley of type and scrambled it to make a type
                                        specimen
                                        bookIt hasey survived not only five centuries, but also the leap.</p>
                                    <h5>Robert Smith</h5>
                                </div>

                                <ul>
                                    <li>Modern an unknown printer took a galley Letraset sheets containing.Pizzhen.</li>
                                    <li>There are many variations of passages of Lorem availa bleLetraset sheets
                                        containing.Pizzhen.</li>
                                    <li>Sure there isn't anything embarrassing hidden Letraset sheets containing.Pizzhen
                                        in the
                                        middle of text.</li>
                                    <li>One Modern an unknown printer took a Letraset sheets containing.Pizzhen galley.
                                    </li>
                                </ul>

                                <div class="row mb_45">
                                    <div class="col-xl-6 col-sm-6">
                                        <div class="details_center_img">
                                            <img src="{{ asset('website/images/details_center_img_2.jpg') }}" alt="blog details"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-6">
                                        <div class="details_center_img">
                                            <img src="{{ asset('website/images/details_center_img_1.jpg') }}" alt="blog details"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>

                                <p>Pizzhen an unknown printer took a galley of type and scrambled it to make a type
                                    specimen
                                    bookIt hasey survived not only five centuries, but also the leap into electronic
                                    typesetting, remaining essentially unch angedwas popularised in tf Letraset sheets
                                    containing.</p>
                                <p>Pizzhen an unknown printer took a galley of type and scrambled it to make a type
                                    specimen
                                    bookIt hasey survived not only five centurie.</p>
                            </div>
                            <div class="details_tags_share">
                                <ul>
                                    <li><span>Tags:</span></li>
                                    <li><a href="#">Breakfast</a></li>
                                    <li><a href="#">Food</a></li>
                                    <li><a href="#">Drinks</a></li>
                                </ul>
                                <a class="details_share" href="#"><i class="far fa-share-alt"></i> 12 Share</a>
                            </div>
                            <div class="blog_det_comment_area menu_det_review_area mt_80">
                                <h2>(03) Comments</h2>
                                <div class="single_review">
                                    <div class="img">
                                        <img src="{{ asset('website/images/client_img_1.png') }}" alt="Reviewer"
                                            class="img-fluid w-100">
                                    </div>
                                    <div class="text">
                                        <h4>Hasnat Abdullah <span>May 8, 2026</span></h4>
                                        <p>Lorem ipsum is simply free text used by copytyping refreshing.
                                            Neque
                                            porro est is a rem ipsum qu
                                            ia qued inventore veritatis et quasi architecto beatae</p>
                                        <a href="#"><i class="far fa-reply"></i> Reply</a>
                                    </div>
                                </div>
                                <div class="single_review reply">
                                    <div class="img">
                                        <img src="{{ asset('website/images/client_img_2.png') }}" alt="Reviewer"
                                            class="img-fluid w-100">
                                    </div>
                                    <div class="text">
                                        <h4>Sinthis Mou <span>May 8, 2026</span></h4>
                                        <p>Lorem ipsum is simply free text used by copytyping refreshing.
                                            Neque
                                            porro est is a rem ipsum qu
                                            ia qued inventore veritatis et quasi architecto beatae</p>
                                        <a href="#"><i class="far fa-reply"></i> Reply</a>
                                    </div>
                                </div>
                                <div class="single_review">
                                    <div class="img">
                                        <img src="{{ asset('website/images/client_img_1.png') }}" alt="Reviewer"
                                            class="img-fluid w-100">
                                    </div>
                                    <div class="text">
                                        <h4>Samira Khanom <span>May 8, 2026</span></h4>
                                        <p>Lorem ipsum is simply free text used by copytyping refreshing.
                                            Neque
                                            porro est is a rem ipsum qu
                                            ia qued inventore veritatis et quasi architecto beatae</p>
                                        <a href="#"><i class="far fa-reply"></i> Reply</a>
                                    </div>
                                </div>
                            </div>
                            <div class="input_comment_area review_input_area mt_80">
                                <h2>Leave A Comments</h2>
                                <span>There are many variations of passages of Lorem Ipsum available slightly
                                    believable.</span>
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="review_input_box">
                                                <input type="text" placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="review_input_box">
                                                <input type="email" placeholder="Your Email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="review_input_box">
                                                <input type="text" placeholder="Phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="review_input_box">
                                                <input type="text" placeholder="Subject">
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="review_input_box">
                                                <textarea rows="5" placeholder="Type your message"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="common_btn">Submit Review
                                        <span></span></button>
                                </form>
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
                                <div class="sidebar_wizard sidebar_category mt_25">
                                    <h2>Categories</h2>
                                    <ul>
                                        <li><a href="{{ route('website.blogs') }}">Food <span>(04)</span></a></li>
                                        <li><a href="{{ route('website.blogs') }}">Drinks <span>(02)</span></a></li>
                                        <li><a href="{{ route('website.blogs') }}">Burger <span>(09)</span></a></li>
                                        <li><a href="{{ route('website.blogs') }}">Chicken <span>(14)</span></a></li>
                                        <li><a href="{{ route('website.blogs') }}">Pizza <span>(05)</span></a></li>
                                        <li><a href="{{ route('website.blogs') }}">Combo <span>(07)</span></a></li>
                                    </ul>
                                </div>
                                <div class="sidebar_wizard sidebar_post mt_25">
                                    <h2>Recent Paost</h2>
                                    <ul>
                                        <li>
                                            <div class="img">
                                                <img src="{{ asset('website/images/sidebar_post_img_1.jpg') }}" alt="post"
                                                    class="img-fluid w-100">
                                            </div>
                                            <div class="text">
                                                <p><i class="far fa-calendar-alt"></i> May 06, 2026</p>
                                                <a class="title" href="{{ route('website.blog-details') }}">Freshly Served Exploring
                                                    the World of Fresh</a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="img">
                                                <img src="{{ asset('website/images/sidebar_post_img_2.jpg') }}" alt="post"
                                                    class="img-fluid w-100">
                                            </div>
                                            <div class="text">
                                                <p><i class="far fa-calendar-alt"></i> May 06, 2026</p>
                                                <a class="title" href="{{ route('website.blog-details') }}">Innovative Hot Chess raw
                                                    Make Creator.</a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="img">
                                                <img src="{{ asset('website/images/sidebar_post_img_3.jpg') }}" alt="post"
                                                    class="img-fluid w-100">
                                            </div>
                                            <div class="text">
                                                <p><i class="far fa-calendar-alt"></i> May 06, 2026</p>
                                                <a class="title" href="{{ route('website.blog-details') }}">This So Trendy Restaurant
                                                    That Everyone</a>
                                            </div>
                                        </li>
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
                                <div class="sidebar_banner menu_details_banner mt_25">
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
                </div>
            </section>
            <!--==========BLOG DETAILS END===========-->
@endsection
