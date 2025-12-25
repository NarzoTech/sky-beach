<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        return view('website::index');
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('website::about');
    }

    /**
     * Display the menu page.
     */
    public function menu()
    {
        return view('website::menu');
    }

    /**
     * Display the menu details page.
     */
    public function menuDetails()
    {
        return view('website::menu_details');
    }

    /**
     * Display the blogs page.
     */
    public function blogs()
    {
        return view('website::blogs');
    }

    /**
     * Display the blog details page.
     */
    public function blogDetails()
    {
        return view('website::blog_details');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('website::contact');
    }

    /**
     * Display the chefs page.
     */
    public function chefs()
    {
        return view('website::chefs');
    }

    /**
     * Display the cart view page.
     */
    public function cartView()
    {
        return view('website::cart_view');
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
        return view('website::checkout');
    }

    /**
     * Display the FAQ page.
     */
    public function faq()
    {
        return view('website::faq');
    }

    /**
     * Display the reservation page.
     */
    public function reservation()
    {
        return view('website::reservation');
    }

    /**
     * Display the service page.
     */
    public function service()
    {
        return view('website::service');
    }

    /**
     * Display the service details page.
     */
    public function serviceDetails()
    {
        return view('website::service_details');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacyPolicy()
    {
        return view('website::privacy_policy');
    }

    /**
     * Display the terms and conditions page.
     */
    public function termsCondition()
    {
        return view('website::terms_condition');
    }

    /**
     * Display the error/404 page.
     */
    public function error()
    {
        return view('website::error');
    }
}
