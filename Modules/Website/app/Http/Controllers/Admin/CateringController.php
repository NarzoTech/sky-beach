<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\CateringPackage;
use Modules\Website\app\Models\CateringInquiry;

class CateringController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CATERING PACKAGES MANAGEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Display packages list
     */
    public function packagesIndex(Request $request)
    {
        $query = CateringPackage::withCount('inquiries');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $packages = $query->ordered()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => CateringPackage::count(),
            'active' => CateringPackage::where('is_active', true)->count(),
            'featured' => CateringPackage::where('is_featured', true)->count(),
            'total_inquiries' => CateringInquiry::count(),
        ];

        return view('website::admin.catering.packages.index', compact('packages', 'stats'));
    }

    /**
     * Show create package form
     */
    public function packagesCreate()
    {
        return view('website::admin.catering.packages.create');
    }

    /**
     * Store new package
     */
    public function packagesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catering_packages,slug',
            'description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'min_guests' => 'required|integer|min:1',
            'max_guests' => 'required|integer|gte:min_guests',
            'price_per_person' => 'required|numeric|min:0',
            'includes' => 'nullable|array',
            'includes.*' => 'string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = upload_image($request->file('image'), 'catering');
        }

        // Filter empty includes
        if (isset($validated['includes'])) {
            $validated['includes'] = array_values(array_filter($validated['includes']));
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        CateringPackage::create($validated);

        return redirect()->route('admin.restaurant.catering.packages.index')
            ->with('success', __('Catering package created successfully.'));
    }

    /**
     * Show edit package form
     */
    public function packagesEdit(CateringPackage $package)
    {
        return view('website::admin.catering.packages.edit', compact('package'));
    }

    /**
     * Update package
     */
    public function packagesUpdate(Request $request, CateringPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catering_packages,slug,' . $package->id,
            'description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'min_guests' => 'required|integer|min:1',
            'max_guests' => 'required|integer|gte:min_guests',
            'price_per_person' => 'required|numeric|min:0',
            'includes' => 'nullable|array',
            'includes.*' => 'string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = upload_image($request->file('image'), 'catering', $package->image);
        }

        // Filter empty includes
        if (isset($validated['includes'])) {
            $validated['includes'] = array_values(array_filter($validated['includes']));
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        $package->update($validated);

        return redirect()->route('admin.restaurant.catering.packages.index')
            ->with('success', __('Catering package updated successfully.'));
    }

    /**
     * Delete package
     */
    public function packagesDestroy(CateringPackage $package)
    {
        delete_image($package->image);
        $package->delete();

        return redirect()->route('admin.restaurant.catering.packages.index')
            ->with('success', __('Catering package deleted successfully.'));
    }

    /*
    |--------------------------------------------------------------------------
    | CATERING INQUIRIES MANAGEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Display inquiries list
     */
    public function inquiriesIndex(Request $request)
    {
        $query = CateringInquiry::with('package');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('event_date', [$request->from_date, $request->to_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => CateringInquiry::count(),
            'pending' => CateringInquiry::pending()->count(),
            'quoted' => CateringInquiry::byStatus('quoted')->count(),
            'confirmed' => CateringInquiry::byStatus('confirmed')->count(),
        ];

        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::admin.catering.inquiries.index', compact('inquiries', 'stats', 'eventTypes'));
    }

    /**
     * Show inquiry details
     */
    public function inquiriesShow(CateringInquiry $inquiry)
    {
        $inquiry->load('package');

        return view('website::admin.catering.inquiries.show', compact('inquiry'));
    }

    /**
     * Update inquiry status
     */
    public function inquiriesUpdateStatus(Request $request, CateringInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,quoted,confirmed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $inquiry->admin_notes,
        ];

        // Set timestamps based on status
        switch ($validated['status']) {
            case 'contacted':
                if (!$inquiry->contacted_at) {
                    $updateData['contacted_at'] = now();
                }
                break;
            case 'confirmed':
                if (!$inquiry->confirmed_at) {
                    $updateData['confirmed_at'] = now();
                }
                break;
        }

        $inquiry->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Inquiry status updated successfully.'),
            ]);
        }

        return redirect()->back()->with('success', __('Inquiry status updated successfully.'));
    }

    /**
     * Save quotation for inquiry
     */
    public function inquiriesSaveQuotation(Request $request, CateringInquiry $inquiry)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'quotation_notes' => 'nullable|string|max:1000',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Calculate totals
        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');

        // Calculate discount
        $discountAmount = 0;
        if (!empty($validated['discount'])) {
            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = $subtotal * ($validated['discount'] / 100);
            } else {
                $discountAmount = $validated['discount'];
            }
        }

        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = $afterDiscount * ($taxRate / 100);

        // Delivery fee
        $deliveryFee = $validated['delivery_fee'] ?? 0;

        // Grand total
        $grandTotal = $afterDiscount + $taxAmount + $deliveryFee;

        $inquiry->update([
            'status' => 'quoted',
            'quoted_amount' => $grandTotal,
            'quotation_items' => $items,
            'quotation_subtotal' => $subtotal,
            'quotation_discount' => $validated['discount'] ?? 0,
            'quotation_discount_type' => $validated['discount_type'],
            'quotation_tax_rate' => $taxRate,
            'quotation_tax_amount' => $taxAmount,
            'quotation_delivery_fee' => $deliveryFee,
            'quotation_notes' => $validated['quotation_notes'] ?? null,
            'quotation_valid_until' => $validated['valid_until'] ?? null,
            'quoted_at' => $inquiry->quoted_at ?? now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quotation saved successfully.'),
                'quoted_amount' => $grandTotal,
            ]);
        }

        return redirect()->back()->with('success', __('Quotation saved successfully.'));
    }

    /**
     * Delete inquiry
     */
    public function inquiriesDestroy(CateringInquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.restaurant.catering.inquiries.index')
            ->with('success', __('Inquiry deleted successfully.'));
    }

    /**
     * Export inquiries to CSV
     */
    public function inquiriesExport(Request $request)
    {
        $query = CateringInquiry::with('package');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('event_date', [$request->from_date, $request->to_date]);
        }

        $inquiries = $query->latest()->get();

        $filename = 'catering_inquiries_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($inquiries) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Inquiry #',
                'Date',
                'Name',
                'Email',
                'Phone',
                'Package',
                'Event Type',
                'Event Date',
                'Guest Count',
                'Venue',
                'Status',
                'Quoted Amount',
                'Special Requirements',
            ]);

            foreach ($inquiries as $inquiry) {
                fputcsv($file, [
                    $inquiry->inquiry_number,
                    $inquiry->created_at->format('Y-m-d H:i:s'),
                    $inquiry->name,
                    $inquiry->email,
                    $inquiry->phone,
                    $inquiry->package->name ?? 'Custom',
                    $inquiry->event_type_label,
                    $inquiry->event_date->format('Y-m-d'),
                    $inquiry->guest_count,
                    $inquiry->venue_address,
                    $inquiry->status_label,
                    $inquiry->quoted_amount,
                    $inquiry->special_requirements,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | CATERING QUOTATIONS MANAGEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Display quotations list
     */
    public function quotationsIndex(Request $request)
    {
        $query = CateringInquiry::hasQuotation()->with('package');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('inquiry_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('quoted_at', [$request->from_date, $request->to_date]);
        }

        $quotations = $query->latest('quoted_at')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => CateringInquiry::hasQuotation()->count(),
            'pending' => CateringInquiry::hasQuotation()->pending()->count(),
            'quoted' => CateringInquiry::hasQuotation()->byStatus('quoted')->count(),
            'confirmed' => CateringInquiry::hasQuotation()->byStatus('confirmed')->count(),
            'total_value' => CateringInquiry::hasQuotation()->sum('quoted_amount'),
        ];

        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::admin.catering.quotations.index', compact('quotations', 'stats', 'eventTypes'));
    }

    /**
     * Show create quotation form
     */
    public function quotationsCreate()
    {
        $packages = CateringPackage::where('is_active', true)->ordered()->get();
        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::admin.catering.quotations.create', compact('packages', 'eventTypes'));
    }

    /**
     * Store new quotation
     */
    public function quotationsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'event_type' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'guest_count' => 'required|integer|min:1',
            'venue_address' => 'nullable|string|max:500',
            'package_id' => 'nullable|exists:catering_packages,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'quotation_notes' => 'nullable|string|max:2000',
            'quotation_terms' => 'nullable|string|max:2000',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Calculate totals
        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');

        // Calculate discount
        $discountAmount = 0;
        if (!empty($validated['discount'])) {
            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = $subtotal * ($validated['discount'] / 100);
            } else {
                $discountAmount = $validated['discount'];
            }
        }

        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = $afterDiscount * ($taxRate / 100);

        // Delivery fee
        $deliveryFee = $validated['delivery_fee'] ?? 0;

        // Grand total
        $grandTotal = $afterDiscount + $taxAmount + $deliveryFee;

        $inquiry = CateringInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'guest_count' => $validated['guest_count'],
            'venue_address' => $validated['venue_address'] ?? null,
            'package_id' => $validated['package_id'] ?? null,
            'status' => 'quoted',
            'quotation_number' => CateringInquiry::generateQuotationNumber(),
            'quoted_amount' => $grandTotal,
            'quotation_items' => $items,
            'quotation_subtotal' => $subtotal,
            'quotation_discount' => $validated['discount'] ?? 0,
            'quotation_discount_type' => $validated['discount_type'],
            'quotation_tax_rate' => $taxRate,
            'quotation_tax_amount' => $taxAmount,
            'quotation_delivery_fee' => $deliveryFee,
            'quotation_notes' => $validated['quotation_notes'] ?? null,
            'quotation_terms' => $validated['quotation_terms'] ?? null,
            'quotation_valid_until' => $validated['valid_until'] ?? null,
            'quoted_at' => now(),
        ]);

        return redirect()->route('admin.restaurant.catering.quotations.show', $inquiry)
            ->with('success', __('Quotation created successfully.'));
    }

    /**
     * Show quotation details
     */
    public function quotationsShow(CateringInquiry $quotation)
    {
        $quotation->load('package');

        return view('website::admin.catering.quotations.show', compact('quotation'));
    }

    /**
     * Show edit quotation form
     */
    public function quotationsEdit(CateringInquiry $quotation)
    {
        $quotation->load('package');
        $packages = CateringPackage::where('is_active', true)->ordered()->get();
        $eventTypes = CateringInquiry::EVENT_TYPES;

        return view('website::admin.catering.quotations.edit', compact('quotation', 'packages', 'eventTypes'));
    }

    /**
     * Update quotation
     */
    public function quotationsUpdate(Request $request, CateringInquiry $quotation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'event_type' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'guest_count' => 'required|integer|min:1',
            'venue_address' => 'nullable|string|max:500',
            'package_id' => 'nullable|exists:catering_packages,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'quotation_notes' => 'nullable|string|max:2000',
            'quotation_terms' => 'nullable|string|max:2000',
            'valid_until' => 'nullable|date',
        ]);

        // Calculate totals
        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');

        // Calculate discount
        $discountAmount = 0;
        if (!empty($validated['discount'])) {
            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = $subtotal * ($validated['discount'] / 100);
            } else {
                $discountAmount = $validated['discount'];
            }
        }

        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = $afterDiscount * ($taxRate / 100);

        // Delivery fee
        $deliveryFee = $validated['delivery_fee'] ?? 0;

        // Grand total
        $grandTotal = $afterDiscount + $taxAmount + $deliveryFee;

        $quotation->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'guest_count' => $validated['guest_count'],
            'venue_address' => $validated['venue_address'] ?? null,
            'package_id' => $validated['package_id'] ?? null,
            'quoted_amount' => $grandTotal,
            'quotation_items' => $items,
            'quotation_subtotal' => $subtotal,
            'quotation_discount' => $validated['discount'] ?? 0,
            'quotation_discount_type' => $validated['discount_type'],
            'quotation_tax_rate' => $taxRate,
            'quotation_tax_amount' => $taxAmount,
            'quotation_delivery_fee' => $deliveryFee,
            'quotation_notes' => $validated['quotation_notes'] ?? null,
            'quotation_terms' => $validated['quotation_terms'] ?? null,
            'quotation_valid_until' => $validated['valid_until'] ?? null,
        ]);

        return redirect()->route('admin.restaurant.catering.quotations.show', $quotation)
            ->with('success', __('Quotation updated successfully.'));
    }

    /**
     * Delete quotation
     */
    public function quotationsDestroy(CateringInquiry $quotation)
    {
        $quotation->delete();

        return redirect()->route('admin.restaurant.catering.quotations.index')
            ->with('success', __('Quotation deleted successfully.'));
    }

    /**
     * Print quotation
     */
    public function quotationsPrint(CateringInquiry $quotation)
    {
        $quotation->load('package');
        $setting = \Modules\GlobalSetting\app\Models\Setting::first();

        return view('website::admin.catering.quotations.print', compact('quotation', 'setting'));
    }

    /**
     * Generate PDF quotation
     */
    public function quotationsPdf(CateringInquiry $quotation)
    {
        $quotation->load('package');
        $setting = \Modules\GlobalSetting\app\Models\Setting::first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('website::admin.catering.quotations.pdf', compact('quotation', 'setting'));

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    /**
     * Calculate guest estimate
     */
    public function quotationsGuestEstimate(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'nullable|exists:catering_packages,id',
            'guest_count' => 'required|integer|min:1',
            'price_per_person' => 'nullable|numeric|min:0',
        ]);

        $pricePerPerson = $validated['price_per_person'] ?? 0;

        if (!empty($validated['package_id']) && empty($validated['price_per_person'])) {
            $package = CateringPackage::find($validated['package_id']);
            if ($package) {
                $pricePerPerson = $package->price_per_person;
            }
        }

        $estimate = $pricePerPerson * $validated['guest_count'];

        return response()->json([
            'success' => true,
            'price_per_person' => $pricePerPerson,
            'guest_count' => $validated['guest_count'],
            'estimate' => $estimate,
            'formatted_estimate' => currency($estimate),
        ]);
    }
}
