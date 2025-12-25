<!--==========FOOTER START===========-->
<footer class="pt_100 mt_120 xs_mt_100">
    <div class="container">
        <div class="row justify-content-between wow fadeInUp">
            <div class="col-lg-3 col-md-4">
                <div class="footer_info">
                    <a class="footer_logo" href="{{ route('website.index') }}">
                        <img src="{{ asset('website/images/footer_logo.png') }}" alt="CTAKE" class="img-fluid w-100">
                    </a>
                    <p>Cras incident lobotids feudist makes viramas sagittas eu valuta.</p>
                    <ul>
                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>Our Menu</h3>
                    <ul>
                        <li><a href="#">Breakfast</a></li>
                        <li><a href="#">Lunch</a></li>
                        <li><a href="#">Dinner</a></li>
                        <li><a href="#">Vegetable</a></li>
                        <li><a href="#">Korean Food</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-4">
                <div class="footer_link">
                    <h3>Resources</h3>
                    <ul>
                        <li><a href="{{ route('website.index') }}">Home</a></li>
                        <li><a href="{{ route('website.about') }}">About</a></li>
                        <li><a href="{{ route('website.contact') }}">Contact</a></li>
                        <li><a href="{{ route('website.blogs') }}">Blog</a></li>
                        <li><a href="{{ route('website.service') }}">Services</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-9">
                <div class=" footer_post">
                    <h3>Recent Post</h3>
                    <ul>
                        <li>
                            <div class="img">
                                <img src="{{ asset('website/images/footer_post_img_1.jpg') }}" alt="post"
                                    class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <p><i class="far fa-clock"></i> March 24, 2026</p>
                                <a href="#">THE WONDERS OF THAI CUISINE SWEET, SALTY & SOUR</a>
                            </div>
                        </li>
                        <li>
                            <div class="img">
                                <img src="{{ asset('website/images/footer_post_img_2.jpg') }}" alt="post"
                                    class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <p><i class="far fa-clock"></i> March 24, 2026</p>
                                <a href="#">PAIRING WINE WITH INDIAN FOOD: TIPS FROM A SOMMELIER</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_copyright_area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer_copyright">
                        <p>Copyright © CTAKE {{ date('Y') }}. All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--==========FOOTER END===========-->
