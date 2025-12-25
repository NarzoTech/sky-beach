@extends('menu::frontend.layouts.menu-layout')

@section('title', __('Combo Deals'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-gift mr-2"></i> {{ __('Combo Deals') }}
            </h1>
            <p class="text-xl text-orange-100">{{ __('Save more with our special combo offers') }}</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if ($combos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($combos as $combo)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden menu-card">
                        @if ($combo->image)
                            <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}"
                                class="w-full h-56 object-cover">
                        @else
                            <div class="w-full h-56 bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center">
                                <i class="fas fa-gift text-white text-6xl"></i>
                            </div>
                        @endif

                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $combo->name }}</h2>

                            @if ($combo->description)
                                <p class="text-gray-600 mb-4">{{ $combo->description }}</p>
                            @endif

                            <!-- Included Items -->
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-500 mb-2">{{ __('Includes') }}:</h4>
                                <ul class="space-y-1">
                                    @foreach ($combo->items->take(4) as $comboItem)
                                        <li class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            {{ $comboItem->quantity }}x {{ $comboItem->menuItem->name ?? 'Item' }}
                                            @if ($comboItem->variant)
                                                <span class="text-gray-400 ml-1">({{ $comboItem->variant->name }})</span>
                                            @endif
                                        </li>
                                    @endforeach
                                    @if ($combo->items->count() > 4)
                                        <li class="text-sm text-orange-600">
                                            +{{ $combo->items->count() - 4 }} {{ __('more items') }}
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Duration -->
                            @if ($combo->end_date)
                                <div class="mb-4 p-2 bg-red-50 rounded-lg">
                                    <p class="text-sm text-red-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ __('Ends') }}: {{ $combo->end_date->format('M d, Y') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Pricing -->
                            <div class="flex items-end justify-between mb-4">
                                <div>
                                    <p class="text-gray-400 text-sm line-through">{{ number_format($combo->original_price, 2) }}</p>
                                    <p class="text-3xl font-bold text-orange-600">{{ number_format($combo->combo_price, 2) }}</p>
                                </div>
                                @if ($combo->savings_percentage > 0)
                                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded-full">
                                        <span class="font-bold">{{ number_format($combo->savings_percentage, 0) }}%</span>
                                        <span class="text-sm">OFF</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Savings -->
                            @if ($combo->savings > 0)
                                <p class="text-center text-green-600 font-semibold mb-4">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ __('You save') }} {{ number_format($combo->savings, 2) }}!
                                </p>
                            @endif

                            <a href="{{ route('menu.combo.detail', $combo->slug) }}"
                                class="block w-full bg-orange-500 text-white text-center py-3 rounded-xl font-semibold hover:bg-orange-600 transition">
                                {{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-gift text-gray-300 text-8xl mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-500 mb-4">{{ __('No active combos at the moment') }}</h2>
                <p class="text-gray-400 mb-6">{{ __('Check back later for our special offers!') }}</p>
                <a href="{{ route('menu.index') }}" class="inline-block bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition">
                    {{ __('Browse Menu') }}
                </a>
            </div>
        @endif
    </div>
@endsection
