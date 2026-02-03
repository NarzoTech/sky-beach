@extends('menu::frontend.layouts.menu-layout')

@section('title', __('Search Results'))

@section('content')
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
                                    class="block py-2 px-3 rounded-lg text-gray-600 hover:bg-gray-100">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Search Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        {{ __('Search Results') }}
                    </h1>
                    @if ($query)
                        <p class="text-gray-600">
                            {{ __('Showing results for') }}: "<span class="font-semibold">{{ $query }}</span>"
                            <span class="text-gray-400">({{ $items->total() }} {{ __('items found') }})</span>
                        </p>
                    @endif
                </div>

                <!-- Search Form -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <form action="{{ route('menu.search') }}" method="GET">
                        <div class="flex gap-4">
                            <div class="flex-1 relative">
                                <input type="text" name="q" value="{{ $query }}"
                                    placeholder="{{ __('Search for dishes, ingredients...') }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-semibold hover:bg-orange-600 transition">
                                {{ __('Search') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Results Grid -->
                @if ($items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($items as $item)
                            <a href="{{ route('menu.item', $item->slug) }}" class="block">
                                <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card h-full">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                        class="w-full h-48 object-cover">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <h3 class="font-bold text-gray-800">{{ $item->name }}</h3>
                                            @if ($item->is_spicy)
                                                <span class="badge-dietary badge-spicy">
                                                    <i class="fas fa-pepper-hot"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($item->category)
                                            <p class="text-orange-500 text-sm mb-2">{{ $item->category->name }}</p>
                                        @endif
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item->short_description }}</p>

                                        <!-- Dietary badges -->
                                        <div class="flex gap-2 mb-3">
                                            @if ($item->is_vegetarian)
                                                <span class="badge-dietary badge-vegetarian">
                                                    <i class="fas fa-leaf mr-1"></i>
                                                </span>
                                            @endif
                                            @if ($item->is_vegan)
                                                <span class="badge-dietary badge-vegan">V</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <span class="text-orange-600 font-bold text-lg">{{ number_format($item->base_price, 2) }}</span>
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
                        {{ $items->appends(['q' => $query])->links() }}
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-xl">
                        <i class="fas fa-search text-gray-300 text-6xl mb-6"></i>
                        <h2 class="text-2xl font-bold text-gray-500 mb-4">{{ __('No results found') }}</h2>
                        @if ($query)
                            <p class="text-gray-400 mb-6">{{ __('Try different keywords or browse our categories') }}</p>
                        @else
                            <p class="text-gray-400 mb-6">{{ __('Enter a search term to find menu items') }}</p>
                        @endif
                        <a href="{{ route('menu.index') }}" class="inline-block bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition">
                            {{ __('Browse Menu') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
