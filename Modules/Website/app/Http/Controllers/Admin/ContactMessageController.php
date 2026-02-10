<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('restaurant.contact-message.view');
        $query = ContactMessage::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(20);

        return view('website::admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        checkAdminHasPermissionAndThrowException('restaurant.contact-message.view');
        // Mark as read if unread
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('website::admin.contact-messages.show', compact('contactMessage'));
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        checkAdminHasPermissionAndThrowException('restaurant.contact-message.view');
        $validated = $request->validate([
            'status' => 'required|in:unread,read,replied',
        ]);

        $contactMessage->update($validated);

        return redirect()->back()->with('success', 'Status updated successfully');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        checkAdminHasPermissionAndThrowException('restaurant.contact-message.delete');
        $contactMessage->delete();

        return redirect()->route('admin.restaurant.contact-messages.index')
            ->with('success', 'Contact message deleted successfully');
    }
}
