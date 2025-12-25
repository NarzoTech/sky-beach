<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Chef;

class ChefController extends Controller
{
    public function index()
    {
        $chefs = Chef::orderBy('order')->paginate(20);
        return view('website::admin.chefs.index', compact('chefs'));
    }

    public function create()
    {
        return view('website::admin.chefs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'experience_years' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('chefs', 'public');
        }

        Chef::create($validated);

        return redirect()->route('admin.chefs.index')
            ->with('success', 'Chef created successfully');
    }

    public function edit(Chef $chef)
    {
        return view('website::admin.chefs.edit', compact('chef'));
    }

    public function update(Request $request, Chef $chef)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'experience_years' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('chefs', 'public');
        }

        $chef->update($validated);

        return redirect()->route('admin.chefs.index')
            ->with('success', 'Chef updated successfully');
    }

    public function destroy(Chef $chef)
    {
        $chef->delete();
        return redirect()->route('admin.chefs.index')
            ->with('success', 'Chef deleted successfully');
    }
}
