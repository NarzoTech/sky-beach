<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\ServiceFaq;
use Modules\Website\app\Models\WebsiteService;

class ServiceFaqController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceFaq::with('service');

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $faqs = $query->orderBy('service_id')->orderBy('order')->paginate(20);
        $services = WebsiteService::active()->ordered()->get();

        return view('website::admin.service-faqs.index', compact('faqs', 'services'));
    }

    public function create()
    {
        $services = WebsiteService::active()->ordered()->get();
        return view('website::admin.service-faqs.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:website_services,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        ServiceFaq::create($validated);

        return redirect()->route('admin.restaurant.service-faqs.index')
            ->with('success', 'Service FAQ created successfully');
    }

    public function edit(ServiceFaq $serviceFaq)
    {
        $services = WebsiteService::active()->ordered()->get();
        return view('website::admin.service-faqs.edit', compact('serviceFaq', 'services'));
    }

    public function update(Request $request, ServiceFaq $serviceFaq)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:website_services,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $serviceFaq->update($validated);

        return redirect()->route('admin.restaurant.service-faqs.index')
            ->with('success', 'Service FAQ updated successfully');
    }

    public function destroy(ServiceFaq $serviceFaq)
    {
        $serviceFaq->delete();
        return redirect()->route('admin.restaurant.service-faqs.index')
            ->with('success', 'Service FAQ deleted successfully');
    }
}
