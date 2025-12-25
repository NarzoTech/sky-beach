<!--==========MENU START===========-->
<nav class="navbar navbar-expand-lg main_menu">
    <div class="container">
        <a class="navbar-brand" href="{{ route('website.index') }}">
            <img src="{{ asset('website/images/logo.png') }}" alt="CTAKE" class="img-fluid w-100">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="fas fa-bars bar_icon"></i>
            <i class="far fa-times close_icon"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav m-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.index') ? 'active' : '' }}" href="{{ route('website.index') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.menu') ? 'active' : '' }}" href="{{ route('website.menu') }}">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.about') ? 'active' : '' }}" href="{{ route('website.about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pages <i class="fal fa-plus"></i></a>
                    <ul class="droap_menu">
                        <li><a href="{{ route('website.blogs') }}">blogs</a></li>
                        <li><a href="{{ route('website.chefs') }}">chefs</a></li>
                        <li><a href="{{ route('website.cart-view') }}">cart view</a></li>
                        <li><a href="{{ route('website.checkout') }}">checkout</a></li>
                        <li><a href="{{ route('website.faq') }}">FAQ's</a></li>
                        <li><a href="{{ route('website.reservation') }}">reservation</a></li>
                        <li><a href="{{ route('website.service') }}">service</a></li>
                        <li><a href="{{ route('website.service-details', 'sample-service') }}">service details</a></li>
                        <li><a href="{{ route('website.privacy-policy') }}">privacy policy</a></li>
                        <li><a href="{{ route('website.terms-condition') }}">terms & condition</a></li>
                        <li><a href="{{ route('website.error') }}">error/404</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.contact') ? 'active' : '' }}" href="{{ route('website.contact') }}">Contact</a>
                </li>
            </ul>
            <ul class="menu_right">
                <li>
                    <a class="menu_search"><i class="far fa-search"></i></a>
                </li>
                <li>
                    <a class="menu_cart" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                        aria-controls="offcanvasRight"><i class="far fa-shopping-basket"></i> <span
                            class="qnty">15</span></a>
                </li>
                <li>
                    <a class="menu_order common_btn" href="{{ route('website.reservation') }}">
                        reserve now
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="menu_search_area">
    <form>
        <input type="text" placeholder="Search Item...">
        <button class="common_btn" type="submit">Search</button>
        <span class="close_search"><i class="far fa-times"></i></span>
    </form>
</div>

<div class="mini_cart">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel"> my cart <span>(05)</span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                    class="far fa-times"></i></button>
        </div>
        <div class="offcanvas-body">
            <ul>
                <li>
                    <div class="img">
                        <img src="{{ asset('website/images/menu_img_1.jpg') }}" alt="product" class="img-fluid w-100">
                    </div>
                    <div class="text">
                        <h5>Chicken Thai Biriyani</h5>
                        <p>$99 <span>Qty: 1</span></p>
                    </div>
                    <span class="close_cart"><i class="far fa-times"></i></span>
                </li>
                <li>
                    <div class="img">
                        <img src="{{ asset('website/images/menu_img_2.jpg') }}" alt="product" class="img-fluid w-100">
                    </div>
                    <div class="text">
                        <h5>Beef Masala</h5>
                        <p>$85 <span>Qty: 2</span></p>
                    </div>
                    <span class="close_cart"><i class="far fa-times"></i></span>
                </li>
                <li>
                    <div class="img">
                        <img src="{{ asset('website/images/menu_img_3.jpg') }}" alt="product" class="img-fluid w-100">
                    </div>
                    <div class="text">
                        <h5>Dal Makhani</h5>
                        <p>$75 <span>Qty: 1</span></p>
                    </div>
                    <span class="close_cart"><i class="far fa-times"></i></span>
                </li>
                <li>
                    <div class="img">
                        <img src="{{ asset('website/images/menu_img_4.jpg') }}" alt="product" class="img-fluid w-100">
                    </div>
                    <div class="text">
                        <h5>Chicken Chowmein</h5>
                        <p>$65 <span>Qty: 2</span></p>
                    </div>
                    <span class="close_cart"><i class="far fa-times"></i></span>
                </li>
                <li>
                    <div class="img">
                        <img src="{{ asset('website/images/menu_img_5.jpg') }}" alt="product" class="img-fluid w-100">
                    </div>
                    <div class="text">
                        <h5>Beef Burger</h5>
                        <p>$55 <span>Qty: 1</span></p>
                    </div>
                    <span class="close_cart"><i class="far fa-times"></i></span>
                </li>
            </ul>
            <div class="mini_cart_button">
                <h6>Total <span>$569</span></h6>
                <a class="common_btn" href="{{ route('website.cart-view') }}">view cart</a>
                <a class="common_btn" href="{{ route('website.checkout') }}">checkout</a>
            </div>
        </div>
    </div>
</div>
<!--==========MENU END===========-->
