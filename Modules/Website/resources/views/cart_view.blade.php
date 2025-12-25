@extends('website::layouts.master')

@section('title', 'cart_view - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>cart view</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">cart view</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========CART VIEW START===========-->
            <section class="cart_view mt_115 xs_mt_95">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-lg-12">
                            <div class="cart_list">
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="pro_img">Image</th>

                                                <th class="pro_name">Product Details</th>

                                                <th class="pro_tk">Price</th>

                                                <th class="pro_select">Quantity</th>

                                                <th class="pro_tk">Subtotal</th>

                                                <th class="pro_icon">
                                                    <a class="clear_all" href="#">Clear</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="pro_img">
                                                    <img src="{{ asset('website/images/cart_img_1.png') }}" alt="product"
                                                        class="img-fluid w-100">
                                                </td>

                                                <td class="pro_name">
                                                    <a href="#">Hyderabadi Biryani</a>
                                                    <span>medium</span>
                                                    <p>coca-cola</p>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$180.00</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button><i class="fal fa-minus"></i></button>
                                                        <input type="text" placeholder="1">
                                                        <button><i class="fal fa-plus"></i></button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$180,00</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#"><i class="far fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pro_img">
                                                    <img src="{{ asset('website/images/cart_img_2.png') }}" alt="product"
                                                        class="img-fluid w-100">
                                                </td>

                                                <td class="pro_name">
                                                    <a href="#">Chicken Masala</a>
                                                    <span>small</span>
                                                    <p>7up</p>
                                                </td>
                                                <td class="pro_tk">
                                                    <h6>$140.00</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button class="btn btn-danger"><i
                                                                class="fal fa-minus"></i></button>
                                                        <input type="text" placeholder="1">
                                                        <button class="btn btn-success"><i
                                                                class="fal fa-plus"></i></button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$140,00</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#"><i class="far fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pro_img">
                                                    <img src="{{ asset('website/images/cart_img_3.png') }}" alt="product"
                                                        class="img-fluid w-100">
                                                </td>

                                                <td class="pro_name">
                                                    <a href="#">Daria Shevtsova</a>
                                                    <span>large</span>
                                                    <p>7up</p>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$220.00</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button class="btn btn-danger"><i
                                                                class="fal fa-minus"></i></button>
                                                        <input type="text" placeholder="1">
                                                        <button class="btn btn-success"><i
                                                                class="fal fa-plus"></i></button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$220,00</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#"><i class="far fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pro_img">
                                                    <img src="{{ asset('website/images/cart_img_4.png') }}" alt="product"
                                                        class="img-fluid w-100">
                                                </td>

                                                <td class="pro_name">
                                                    <a href="#">Hyderabadi Biryani</a>
                                                    <span>medium</span>
                                                    <p>coca-cola</p>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$150.00</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button class="btn btn-danger"><i
                                                                class="fal fa-minus"></i></button>
                                                        <input type="text" placeholder="1">
                                                        <button class="btn btn-success"><i
                                                                class="fal fa-plus"></i></button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$150.00</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#"><i class="far fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pro_img">
                                                    <img src="{{ asset('website/images/cart_img_5.png') }}" alt="product"
                                                        class="img-fluid w-100">
                                                </td>

                                                <td class="pro_name">
                                                    <a href="#">Hyderabadi Biryani</a>
                                                    <span>medium</span>
                                                    <p>7up</p>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$150.00</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button class="btn btn-danger"><i
                                                                class="fal fa-minus"></i></button>
                                                        <input type="text" placeholder="1">
                                                        <button class="btn btn-success"><i
                                                                class="fal fa-plus"></i></button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>$150.00</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#"><i class="far fa-times"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" cart_list_footer_button mt_60">
                        <div class="row wow fadeInUp">
                            <div class="col-xl-8 col-md-6 col-lg-7">
                                <form>
                                    <input type="text" placeholder="Coupon Code">
                                    <button class="common_btn" type="submit">apply Coupon</button>
                                </form>
                            </div>
                            <div class="col-xl-4 col-md-6 col-lg-5">
                                <div class="cart_summery">
                                    <h6>total cart (02)</h6>
                                    <p>subtotal: <span>$124.00</span></p>
                                    <p>delivery: <span>$00.00</span></p>
                                    <p>discount: <span>$10.00</span></p>
                                    <p class="total"><span>total:</span> <span>$134.00</span></p>
                                    <a class="common_btn" href="{{ route('website.checkout') }}">checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========CART VIEW START===========-->
@endsection
