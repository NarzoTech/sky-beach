<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order')->paginate(20);
        return view('website::admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('website::admin.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully');
    }

    public function edit(Faq $faq)
    {
        return view('website::admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully');
    }
}
