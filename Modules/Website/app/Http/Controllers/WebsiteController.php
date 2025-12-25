<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Blog;
use Modules\Website\app\Models\Chef;
use Modules\Website\app\Models\WebsiteService;
use Modules\Website\app\Models\Faq;
use Modules\Website\app\Models\RestaurantMenuItem;

class WebsiteController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $featuredMenuItems = RestaurantMenuItem::active()
            ->forWebsite()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();
            
        $featuredChefs = Chef::active()
            ->featured()
            ->ordered()
            ->take(4)
            ->get();
            
        $recentBlogs = Blog::active()
            ->published()
            ->latest()
            ->take(3)
            ->get();
        
        return view('website::index', compact('featuredMenuItems', 'featuredChefs', 'recentBlogs'));
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
        $menuItems = RestaurantMenuItem::active()
            ->forWebsite()
            ->ordered()
            ->get();
            
        $categories = RestaurantMenuItem::active()
            ->forWebsite()
            ->pluck('category')
            ->unique()
            ->filter();
        
        return view('website::menu', compact('menuItems', 'categories'));
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
        $blogs = Blog::active()
            ->published()
            ->latest()
            ->paginate(9);
            
        $featuredBlogs = Blog::active()
            ->published()
            ->featured()
            ->take(3)
            ->get();
        
        return view('website::blogs', compact('blogs', 'featuredBlogs'));
    }

    /**
     * Display the blog details page.
     */
    public function blogDetails($slug = null)
    {
        $blog = Blog::active()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
            
        // Increment views
        $blog->increment('views');
        
        $relatedBlogs = Blog::active()
            ->published()
            ->where('id', '!=', $blog->id)
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        return view('website::blog_details', compact('blog', 'relatedBlogs'));
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
        $chefs = Chef::active()
            ->ordered()
            ->get();
        
        return view('website::chefs', compact('chefs'));
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
        $faqs = Faq::active()
            ->ordered()
            ->get()
            ->groupBy('category');
        
        return view('website::faq', compact('faqs'));
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
        $services = WebsiteService::active()
            ->ordered()
            ->get();
            
        $featuredServices = WebsiteService::active()
            ->featured()
            ->ordered()
            ->get();
        
        return view('website::service', compact('services', 'featuredServices'));
    }

    /**
     * Display the service details page.
     */
    public function serviceDetails($slug = null)
    {
        $service = WebsiteService::active()
            ->where('slug', $slug)
            ->firstOrFail();
            
        $relatedServices = WebsiteService::active()
            ->where('id', '!=', $service->id)
            ->take(3)
            ->get();
        
        return view('website::service_details', compact('service', 'relatedServices'));
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
