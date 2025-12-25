@extends('menu::frontend.layouts.menu-layout')

@section('title', $category->name)

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="text-sm">
                <a href="{{ route('menu.index') }}" class="text-gray-500 hover:text-orange-600">{{ __('Menu') }}</a>
                <span class="mx-2 text-gray-400">/</span>
                @if ($category->parent)
                    <a href="{{ route('menu.category', $category->parent->slug) }}" class="text-gray-500 hover:text-orange-600">
                        {{ $category->parent->name }}
                    </a>
                    <span class="mx-2 text-gray-400">/</span>
                @endif
                <span class="text-gray-800">{{ $category->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">{{ __('Categories') }}</h3>
                    <ul class="space-y-2">
                        @foreach ($allCategories as $cat)
                            <li>
                                <a href="{{ route('menu.category', $cat->slug) }}"
                                    class="block py-2 px-3 rounded-lg {{ $cat->id == $category->id ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $cat->name }}
                                </a>
                                @if ($cat->children->count() > 0)
                                    <ul class="ml-4 mt-1 space-y-1">
                                        @foreach ($cat->children as $child)
                                            <li>
                                                <a href="{{ route('menu.category', $child->slug) }}"
                                                    class="block py-1 px-3 text-sm rounded-lg {{ $child->id == $category->id ? 'bg-orange-100 text-orange-700 font-semibold' : 'text-gray-500 hover:bg-gray-100' }}">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Category Header -->
                <div class="mb-8">
                    @if ($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                            class="w-full h-48 object-cover rounded-xl mb-4">
                    @endif
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
                    @if ($category->description)
                        <p class="text-gray-600">{{ $category->description }}</p>
                    @endif
                </div>

                <!-- Subcategories -->
                @if ($category->children->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Subcategories') }}</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($category->children as $child)
                                <a href="{{ route('menu.category', $child->slug) }}"
                                    class="bg-orange-100 text-orange-700 px-4 py-2 rounded-full hover:bg-orange-200 transition">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Items Grid -->
                @if ($items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($items as $item)
                            <a href="{{ route('menu.item', $item->slug) }}" class="block">
                                <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card h-full">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                            class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400 text-4xl"></i>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <h3 class="font-bold text-gray-800">{{ $item->name }}</h3>
                                            @if ($item->is_spicy)
                                                <span class="badge-dietary badge-spicy">
                                                    <i class="fas fa-pepper-hot"></i> {{ $item->spice_level }}/5
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item->short_description }}</p>

                                        <!-- Dietary badges -->
                                        <div class="flex gap-2 mb-3">
                                            @if ($item->is_vegetarian)
                                                <span class="badge-dietary badge-vegetarian">
                                                    <i class="fas fa-leaf mr-1"></i> {{ __('Vegetarian') }}
                                                </span>
                                            @endif
                                            @if ($item->is_vegan)
                                                <span class="badge-dietary badge-vegan">{{ __('Vegan') }}</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-orange-600 font-bold text-lg">{{ number_format($item->base_price, 2) }}</span>
                                                @if ($item->variants->count() > 0)
                                                    <span class="text-gray-400 text-sm">+{{ $item->variants->count() }} {{ __('variants') }}</span>
                                                @endif
                                            </div>
                                            @if ($item->preparation_time)
                                                <span class="text-gray-400 text-sm">
                                                    <i class="fas fa-clock mr-1"></i>{{ $item->preparation_time }} min
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $items->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-xl">
                        <i class="fas fa-utensils text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500">{{ __('No items found in this category.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
