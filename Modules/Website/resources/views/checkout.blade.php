@extends('website::layouts.master')

@section('title', 'checkout - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>checkout</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">checkout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========CHECKOUT START===========-->
            <section class="checkout pt_110 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 wow fadeInLeft">
                            <div class="checkout_area">
                                <h2>Billing Details</h2>
                                <form action="#">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" placeholder="Fast Name">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" placeholder="Last Name">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="email" placeholder="Your Email">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" placeholder="Your Phone">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" placeholder="Company (Optional)">
                                        </div>
                                        <div class="col-md-6">
                                            <select class="select_2" name="state">
                                                <option value="AL">Select Country</option>
                                                <option value="">Japan</option>
                                                <option value="">Korea</option>
                                                <option value="">Thailand</option>
                                                <option value="">singapore</option>
                                                <option value="">Landon</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="select_2" name="state">
                                                <option value="AL">Select city</option>
                                                <option value="">Dhaka</option>
                                                <option value="">cox's bazal</option>
                                                <option value="">rajshahi</option>
                                                <option value="">khulna</option>
                                                <option value="">pabna</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" placeholder="Zip">
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" placeholder="Address">
                                        </div>
                                        <div class="col-md-12">
                                            <textarea rows="7" placeholder="Additional Information"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-7 wow fadeInRight">
                            <div class="checkout_sidebar">
                                <h2>Your Order</h2>
                                <div class="cart_summery">
                                    <h6>total cart (02)</h6>
                                    <p>subtotal: <span>$124.00</span></p>
                                    <p>delivery: <span>$00.00</span></p>
                                    <p>discount: <span>$10.00</span></p>
                                    <p class="total"><span>total:</span> <span>$134.00</span></p>
                                    <a class="common_btn" href="check_out.html">checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========CHECKOUT END===========-->
@endsection
