@extends('menu::frontend.layouts.menu-layout')

@section('title', __('Our Menu'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Our Menu') }}</h1>
            <p class="text-xl text-orange-100">{{ __('Discover our delicious selection of dishes') }}</p>
        </div>
    </section>

    <!-- Active Combos -->
    @if ($activeCombos->count() > 0)
        <section class="py-12 bg-yellow-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-fire text-orange-500 mr-2"></i> {{ __('Special Combos') }}
                    </h2>
                    <a href="{{ route('menu.combos') }}" class="text-orange-600 hover:text-orange-700 font-semibold">
                        {{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($activeCombos as $combo)
                        <a href="{{ route('menu.combo.detail', $combo->slug) }}" class="block">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card">
                                @if ($combo->image)
                                    <img src="{{ asset($combo->image) }}" alt="{{ $combo->name }}"
                                        class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center">
                                        <i class="fas fa-gift text-white text-4xl"></i>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-800 mb-2">{{ $combo->name }}</h3>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-gray-400 line-through text-sm">{{ number_format($combo->original_price, 2) }}</span>
                                            <span class="text-orange-600 font-bold text-lg ml-2">{{ number_format($combo->combo_price, 2) }}</span>
                                        </div>
                                        @if ($combo->savings_percentage > 0)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                                {{ number_format($combo->savings_percentage, 0) }}% OFF
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Featured Items -->
    @if ($featuredItems->count() > 0)
        <section class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">
                    <i class="fas fa-star text-yellow-500 mr-2"></i> {{ __('Featured Items') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredItems as $item)
                        <a href="{{ route('menu.item', $item->slug) }}" class="block">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card">
                                @if ($item->image)
                                    <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
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
                                                <i class="fas fa-pepper-hot mr-1"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item->short_description }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-orange-600 font-bold text-lg">{{ number_format($item->base_price, 2) }}</span>
                                        <div class="flex gap-1">
                                            @if ($item->is_vegetarian)
                                                <span class="badge-dietary badge-vegetarian" title="{{ __('Vegetarian') }}">
                                                    <i class="fas fa-leaf"></i>
                                                </span>
                                            @endif
                                            @if ($item->is_vegan)
                                                <span class="badge-dietary badge-vegan" title="{{ __('Vegan') }}">V</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Categories -->
    <section class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">{{ __('Browse by Category') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('menu.category', $category->slug) }}" class="block">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card text-center">
                            @if ($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}"
                                    class="w-full h-32 object-cover">
                            @else
                                <div class="w-full h-32 bg-gradient-to-r from-gray-200 to-gray-300 flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-400 text-3xl"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                                @if ($category->items_count ?? false)
                                    <p class="text-gray-500 text-sm">{{ $category->items_count }} {{ __('items') }}</p>
                                @endif
                            </div>
                        </div>
                    </a>

                    @if ($category->children->count() > 0)
                        @foreach ($category->children as $child)
                            <a href="{{ route('menu.category', $child->slug) }}" class="block">
                                <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card text-center border-l-4 border-orange-400">
                                    @if ($child->image)
                                        <img src="{{ asset($child->image) }}" alt="{{ $child->name }}"
                                            class="w-full h-32 object-cover">
                                    @else
                                        <div class="w-full h-32 bg-gradient-to-r from-orange-100 to-orange-200 flex items-center justify-center">
                                            <i class="fas fa-utensils text-orange-400 text-3xl"></i>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-800">{{ $child->name }}</h3>
                                        <p class="text-gray-400 text-xs">{{ $category->name }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
