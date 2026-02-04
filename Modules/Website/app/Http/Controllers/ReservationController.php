<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Website\app\Models\Booking;
use Modules\Website\app\Models\Blog;
use Modules\Menu\app\Models\MenuItem;

class ReservationController extends Controller
{
    /**
     * Display reservation form
     */
    public function index()
    {
        $tablePreferences = Booking::TABLE_PREFERENCES;
        $timeSlots = $this->getAvailableTimeSlots();

        // Pre-fill user data if logged in
        $user = Auth::user();

        // Get gallery items (featured menu items)
        $galleryItems = MenuItem::with('category')
            ->active()
            ->available()
            ->forWebsite()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();

        // Get recent blogs
        $recentBlogs = Blog::active()
            ->published()
            ->latest()
            ->take(3)
            ->get();

        return view('website::reservation', compact('tablePreferences', 'timeSlots', 'user', 'galleryItems', 'recentBlogs'));
    }

    /**
     * Store a new reservation
     */
    public function store(Request $request)
    {
        \Log::info('=== Reservation Store Started ===');
        \Log::info('Request data:', $request->all());

        try {
            \Log::info('Validating request...');
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|string|max:20',
                'booking_date' => 'required|date|after_or_equal:today',
                'booking_time' => 'required|date_format:H:i',
                'number_of_guests' => 'required|integer|min:1|max:20',
                'table_preference' => 'nullable|string|in:' . implode(',', array_keys(Booking::TABLE_PREFERENCES)),
                'special_request' => 'nullable|string|max:500',
            ]);
            \Log::info('Validation passed:', $validated);

            // Check availability
            \Log::info('Checking availability...');
            $isAvailable = $this->checkSlotAvailability(
                $validated['booking_date'],
                $validated['booking_time'],
                $validated['number_of_guests']
            );
            \Log::info('Availability check result: ' . ($isAvailable ? 'Available' : 'Not Available'));

            if (!$isAvailable) {
                \Log::warning('Time slot not available');
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Sorry, the selected time slot is no longer available. Please choose another time.'),
                    ], 400);
                }
                return back()->withInput()->with('error', __('Sorry, the selected time slot is no longer available.'));
            }

            // Create booking
            \Log::info('Creating booking...');
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'number_of_guests' => $validated['number_of_guests'],
                'table_preference' => $validated['table_preference'] ?? 'any',
                'special_request' => $validated['special_request'] ?? null,
                'status' => Booking::STATUS_PENDING,
            ]);
            \Log::info('Booking created successfully. ID: ' . $booking->id . ', Code: ' . $booking->confirmation_code);

            if ($request->ajax() || $request->wantsJson()) {
                \Log::info('Returning JSON success response');
                return response()->json([
                    'success' => true,
                    'message' => __('Reservation submitted successfully!'),
                    'confirmation_code' => $booking->confirmation_code,
                    'redirect_url' => route('website.reservation.success', $booking->confirmation_code),
                ]);
            }

            return redirect()->route('website.reservation.success', $booking->confirmation_code)
                ->with('success', __('Reservation submitted successfully!'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            throw $e; // Let Laravel handle validation exceptions
        } catch (\Exception $e) {
            \Log::error('Reservation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while processing your reservation. Please try again.'),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            return back()->withInput()->with('error', __('An error occurred. Please try again.'));
        }
    }

    /**
     * Check availability for a date/time slot
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'nullable|date_format:H:i',
            'guests' => 'nullable|integer|min:1|max:20',
        ]);

        $date = $request->date;
        $time = $request->time;
        $guests = $request->guests ?? 1;

        // If no specific time, return available slots for the date
        if (!$time) {
            $availableSlots = $this->getAvailableSlotsForDate($date);
            return response()->json([
                'success' => true,
                'available_slots' => $availableSlots,
            ]);
        }

        // Check specific slot
        $isAvailable = $this->checkSlotAvailability($date, $time, $guests);

        return response()->json([
            'success' => true,
            'available' => $isAvailable,
            'message' => $isAvailable
                ? __('This time slot is available!')
                : __('Sorry, this time slot is not available. Please choose another time.'),
        ]);
    }

    /**
     * Display reservation success page
     */
    public function success($code)
    {
        $booking = Booking::where('confirmation_code', $code)->firstOrFail();

        return view('website::reservation_success', compact('booking'));
    }

    /**
     * Display user's reservations
     */
    public function myReservations(Request $request)
    {
        $user = Auth::user();

        $query = Booking::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('email', $user->email);
        })->orderBy('booking_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter upcoming/past
        if ($request->filter === 'upcoming') {
            $query->upcoming();
        } elseif ($request->filter === 'past') {
            $query->past();
        }

        $reservations = $query->paginate(10);

        return view('website::my_reservations', compact('reservations'));
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, $id)
    {
        $user = Auth::user();

        $booking = Booking::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('email', $user->email);
        })->findOrFail($id);

        if (!$booking->canBeCancelled()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This reservation cannot be cancelled. Cancellations must be made at least 2 hours before the reservation time.'),
                ], 400);
            }
            return back()->with('error', __('This reservation cannot be cancelled.'));
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_reason' => $request->reason ?? 'Cancelled by customer',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Reservation cancelled successfully.'),
            ]);
        }

        return redirect()->route('website.reservations.index')
            ->with('success', __('Reservation cancelled successfully.'));
    }

    /**
     * Get available time slots
     */
    private function getAvailableTimeSlots()
    {
        // Restaurant operating hours - can be moved to config/settings
        $slots = [];
        $startHour = 10; // 10 AM
        $endHour = 22;   // 10 PM
        $interval = 30;  // 30 minutes

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $display = date('h:i A', strtotime($time));
                $slots[$time] = $display;
            }
        }

        return $slots;
    }

    /**
     * Get available slots for a specific date
     */
    private function getAvailableSlotsForDate($date)
    {
        $allSlots = $this->getAvailableTimeSlots();
        $maxPerSlot = 5; // Maximum reservations per time slot

        // Count existing bookings for each slot
        $bookedCounts = Booking::forDate($date)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->selectRaw('TIME_FORMAT(booking_time, "%H:%i") as slot, COUNT(*) as count')
            ->groupBy('slot')
            ->pluck('count', 'slot')
            ->toArray();

        $availableSlots = [];
        $now = now();
        $isToday = $date === $now->toDateString();

        foreach ($allSlots as $time => $display) {
            // If today, skip past time slots (add 1 hour buffer)
            if ($isToday) {
                $slotTime = \Carbon\Carbon::parse($date . ' ' . $time);
                if ($slotTime->lte($now->addHour())) {
                    continue;
                }
            }

            $bookedCount = $bookedCounts[$time] ?? 0;
            if ($bookedCount < $maxPerSlot) {
                $availableSlots[] = [
                    'time' => $time,
                    'display' => $display,
                    'available' => $maxPerSlot - $bookedCount,
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * Check if a specific slot is available
     */
    private function checkSlotAvailability($date, $time, $guests)
    {
        $maxPerSlot = 5; // Maximum reservations per time slot
        $maxGuests = 50; // Maximum total guests per time slot

        // Count existing bookings for the slot
        $existingBookings = Booking::forDate($date)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->whereRaw('TIME_FORMAT(booking_time, "%H:%i") = ?', [$time])
            ->get();

        $bookingCount = $existingBookings->count();
        $totalGuests = $existingBookings->sum('number_of_guests');

        // Check if slot is available
        if ($bookingCount >= $maxPerSlot) {
            return false;
        }

        if ($totalGuests + $guests > $maxGuests) {
            return false;
        }

        // Check if it's not in the past
        $slotDateTime = \Carbon\Carbon::parse($date . ' ' . $time);
        if ($slotDateTime->lte(now()->addHour())) {
            return false;
        }

        return true;
    }
}
