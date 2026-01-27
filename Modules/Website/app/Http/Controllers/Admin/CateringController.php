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
            $validated['image'] = $request->file('image')->store('catering', 'public');
            $validated['image'] = 'storage/' . $validated['image'];
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
            $validated['image'] = $request->file('image')->store('catering', 'public');
            $validated['image'] = 'storage/' . $validated['image'];
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
}
