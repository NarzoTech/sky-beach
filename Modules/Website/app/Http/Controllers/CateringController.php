<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\CateringPackage;
use Modules\Website\app\Models\CateringInquiry;

class CateringController extends Controller
{
    /**
     * Display catering packages list
     */
    public function index()
    {
        $featuredPackages = CateringPackage::active()
            ->featured()
            ->ordered()
            ->take(3)
            ->get();

        $packages = CateringPackage::active()
            ->ordered()
            ->get();

        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::catering.index', compact('featuredPackages', 'packages', 'eventTypes'));
    }

    /**
     * Display single package details
     */
    public function show($slug)
    {
        $package = CateringPackage::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPackages = CateringPackage::active()
            ->where('id', '!=', $package->id)
            ->ordered()
            ->take(3)
            ->get();

        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::catering.show', compact('package', 'relatedPackages', 'eventTypes'));
    }

    /**
     * Display custom inquiry form
     */
    public function inquiryForm()
    {
        $packages = CateringPackage::active()->ordered()->get();
        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::catering.inquiry', compact('packages', 'eventTypes'));
    }

    /**
     * Submit catering inquiry
     */
    public function submitInquiry(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'nullable|exists:catering_packages,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'event_type' => 'required|string|in:' . implode(',', array_keys(CateringInquiry::EVENT_TYPES)),
            'event_date' => 'required|date|after:today',
            'event_time' => 'nullable|date_format:H:i',
            'guest_count' => 'required|integer|min:10|max:500',
            'venue_address' => 'nullable|string|max:500',
            'special_requirements' => 'nullable|string|max:1000',
        ]);

        // Validate guest count against package limits if package selected
        if (!empty($validated['package_id'])) {
            $package = CateringPackage::find($validated['package_id']);
            if ($package) {
                if ($validated['guest_count'] < $package->min_guests) {
                    return $this->errorResponse($request, __('Minimum :min guests required for this package.', ['min' => $package->min_guests]));
                }
                if ($validated['guest_count'] > $package->max_guests) {
                    return $this->errorResponse($request, __('Maximum :max guests allowed for this package.', ['max' => $package->max_guests]));
                }
            }
        }

        $inquiry = CateringInquiry::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Your catering inquiry has been submitted successfully!'),
                'inquiry_number' => $inquiry->inquiry_number,
                'redirect_url' => route('website.catering.inquiry.success', $inquiry->inquiry_number),
            ]);
        }

        return redirect()->route('website.catering.inquiry.success', $inquiry->inquiry_number)
            ->with('success', __('Your catering inquiry has been submitted successfully!'));
    }

    /**
     * Display inquiry success page
     */
    public function inquirySuccess($inquiryNumber)
    {
        $inquiry = CateringInquiry::where('inquiry_number', $inquiryNumber)->firstOrFail();

        return view('website::catering.inquiry_success', compact('inquiry'));
    }

    /**
     * Get price estimate (AJAX)
     */
    public function getPriceEstimate(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:catering_packages,id',
            'guest_count' => 'required|integer|min:1',
        ]);

        $package = CateringPackage::find($request->package_id);

        if (!$package) {
            return response()->json(['success' => false, 'message' => __('Package not found')], 404);
        }

        $guestCount = max($package->min_guests, min($package->max_guests, $request->guest_count));
        $totalPrice = $package->calculatePrice($guestCount);

        return response()->json([
            'success' => true,
            'price_per_person' => $package->price_per_person,
            'guest_count' => $guestCount,
            'total_price' => $totalPrice,
            'formatted_price' => '$' . number_format($totalPrice, 2),
            'min_guests' => $package->min_guests,
            'max_guests' => $package->max_guests,
        ]);
    }

    /**
     * Return error response
     */
    private function errorResponse(Request $request, $message)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }

        return back()->withInput()->with('error', $message);
    }
}
