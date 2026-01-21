<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\ServiceContact;

class ServiceContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceContact::with('service')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $contacts = $query->paginate(20);
        $services = \Modules\Website\app\Models\WebsiteService::active()->ordered()->get();

        return view('website::admin.service-contacts.index', compact('contacts', 'services'));
    }

    public function show(ServiceContact $serviceContact)
    {
        $serviceContact->load('service');

        // Mark as read if pending
        if ($serviceContact->status === 'pending') {
            $serviceContact->update(['status' => 'read']);
        }

        return view('website::admin.service-contacts.show', compact('serviceContact'));
    }

    public function updateStatus(Request $request, ServiceContact $serviceContact)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,read,replied',
            'admin_notes' => 'nullable|string',
        ]);

        $serviceContact->update($validated);

        return redirect()->back()->with('success', 'Status updated successfully');
    }

    public function destroy(ServiceContact $serviceContact)
    {
        $serviceContact->delete();
        return redirect()->route('admin.restaurant.service-contacts.index')
            ->with('success', 'Contact inquiry deleted successfully');
    }
}
