<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Booking;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->latest()->paginate(20);
        
        return view('website::admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        return view('website::admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'admin_notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking status updated successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully');
    }
}
