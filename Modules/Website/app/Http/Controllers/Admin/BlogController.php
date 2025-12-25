<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Blog;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(20);
        return view('website::admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('website::admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blogs,slug',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'tags' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog created successfully');
    }

    public function edit(Blog $blog)
    {
        return view('website::admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blogs,slug,' . $blog->id,
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'tags' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog updated successfully');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog deleted successfully');
    }
}
