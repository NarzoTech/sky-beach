<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Blog;
use Modules\Website\app\Models\Chef;
use Modules\Website\app\Models\WebsiteService;
use Modules\Website\app\Models\Faq;
use Modules\Website\app\Models\ServiceContact;
use Modules\Website\app\Models\ServiceFaq;
use Modules\Website\app\Models\ContactMessage;
use Modules\Website\app\Models\RestaurantMenuItem;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuCategory;

class WebsiteController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $featuredMenuItems = MenuItem::with('category')
            ->active()
            ->available()
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
        $chefs = Chef::active()
            ->featured()
            ->ordered()
            ->take(4)
            ->get();

        $blogs = Blog::active()
            ->published()
            ->latest()
            ->take(3)
            ->get();

        return view('website::about', compact('chefs', 'blogs'));
    }

    /**
     * Display the menu page.
     */
    public function menu(Request $request)
    {
        // Get min and max prices from menu items
        $menuPriceRange = MenuItem::where('status', 1)
            ->where('is_available', 1)
            ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
            ->first();

        // Get min and max prices from active combos
        $comboPriceRange = \Modules\Menu\app\Models\Combo::where('is_active', 1)
            ->where('status', 1)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->selectRaw('MIN(combo_price) as min_price, MAX(combo_price) as max_price')
            ->first();

        // Combine both to get overall min/max (min always starts at 0)
        $priceRange = (object) [
            'min_price' => 0,
            'max_price' => max($menuPriceRange->max_price ?? 0, $comboPriceRange->max_price ?? 0),
        ];

        // Get filter parameters - use actual price range as default
        $categorySlug = $request->get('category');
        $search = $request->get('search');
        $minPrice = $request->get('min_price', $priceRange->min_price ?? 0);
        $maxPrice = $request->get('max_price', $priceRange->max_price ?? 1000);
        $sortBy = $request->get('sort_by', 'default'); // default, price_low, price_high, name_asc, name_desc, popular

        // Get all active categories with item counts
        $categories = MenuCategory::active()
            ->ordered()
            ->withCount(['activeMenuItems'])
            ->get();

        // Build query for menu items
        $query = MenuItem::with(['category', 'variants', 'addons'])
            ->where('status', 1)
            ->where('is_available', 1);

        // Apply category filter by slug
        if ($categorySlug) {
            $category = MenuCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('long_description', 'like', "%{$search}%");
            });
        }

        // Apply price filter only if explicitly set in request
        if ($request->has('min_price') || $request->has('max_price')) {
            $query->whereBetween('base_price', [$minPrice, $maxPrice]);
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('display_order', 'asc');
                break;
            default:
                $query->orderBy('display_order', 'asc')
                      ->orderBy('name', 'asc');
                break;
        }
        
        // Get results with pagination
        $menuItems = $query->paginate(9);

        // Get active combo packages with price filter
        $comboQuery = \Modules\Menu\app\Models\Combo::with(['comboItems.menuItem'])
            ->where('is_active', 1)
            ->where('status', 1)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });

        // Apply price filter to combos only if explicitly set
        if ($request->has('min_price') || $request->has('max_price')) {
            $comboQuery->whereBetween('combo_price', [$minPrice, $maxPrice]);
        }

        $combos = $comboQuery->orderBy('created_at', 'desc')->get();

        return view('website::menu', compact('menuItems', 'categories', 'priceRange', 'categorySlug', 'search', 'minPrice', 'maxPrice', 'sortBy', 'combos'));
    }

    /**
     * Display the menu details page.
     */
    public function menuDetails($slug)
    {
        $menuItem = MenuItem::with(['category', 'activeVariants', 'activeAddons'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->where('is_available', 1)
            ->firstOrFail();

        // Get related items from same category
        $relatedItems = MenuItem::with('category')
            ->where('status', 1)
            ->where('is_available', 1)
            ->where('id', '!=', $menuItem->id)
            ->where('category_id', $menuItem->category_id)
            ->inRandomOrder()
            ->take(5)
            ->get();

        // If not enough related items from same category, get popular items
        if ($relatedItems->count() < 5) {
            $additionalItems = MenuItem::with('category')
                ->where('status', 1)
                ->where('is_available', 1)
                ->where('id', '!=', $menuItem->id)
                ->whereNotIn('id', $relatedItems->pluck('id'))
                ->where('is_featured', 1)
                ->inRandomOrder()
                ->take(5 - $relatedItems->count())
                ->get();
            $relatedItems = $relatedItems->concat($additionalItems);
        }

        return view('website::menu_details', compact('menuItem', 'relatedItems'));
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
            ->get();

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
            ->take(4)
            ->get();

        // Get service-specific FAQs first, then general FAQs if none exist
        $serviceFaqs = ServiceFaq::where('service_id', $service->id)
            ->active()
            ->ordered()
            ->get();

        $faqs = Faq::active()
            ->ordered()
            ->take(5)
            ->get();

        return view('website::service_details', compact('service', 'relatedServices', 'faqs', 'serviceFaqs'));
    }

    /**
     * Store a service contact inquiry.
     */
    public function storeServiceContact(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:website_services,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        ServiceContact::create($validated);

        $service = WebsiteService::find($validated['service_id']);

        return redirect()->route('website.service-details', $service->slug)
            ->with('success', 'Your inquiry has been submitted successfully. We will get back to you soon.');
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

    /**
     * Store a contact message.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'newsletter' => 'nullable|boolean',
        ]);

        $validated['newsletter'] = $request->has('newsletter');

        ContactMessage::create($validated);

        return redirect()->route('website.contact')
            ->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
}
