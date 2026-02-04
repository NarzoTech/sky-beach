@extends('menu::frontend.layouts.menu-layout')

@section('title', $combo->name)

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="text-sm">
                <a href="{{ route('website.menu') }}" class="text-gray-500 hover:text-orange-600">{{ __('Menu') }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-800">{{ $combo->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="lg:flex">
                <!-- Image Section -->
                <div class="lg:w-1/2">
                    @if ($combo->image)
                        <img src="{{ asset($combo->image) }}" alt="{{ $combo->name }}"
                            class="w-full h-80 lg:h-full object-cover">
                    @else
                        <div class="w-full h-80 lg:h-full bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center">
                            <i class="fas fa-gift text-white text-8xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="lg:w-1/2 p-8">
                    <div class="flex items-start justify-between mb-4">
                        <h1 class="text-3xl font-bold text-gray-800">{{ $combo->name }}</h1>
                        @if ($combo->savings_percentage > 0)
                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold">
                                {{ number_format($combo->savings_percentage, 0) }}% OFF
                            </span>
                        @endif
                    </div>

                    @if ($combo->description)
                        <p class="text-gray-600 mb-6">{{ $combo->description }}</p>
                    @endif

                    <!-- Duration Banner -->
                    @if ($combo->start_date || $combo->end_date)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
                            <p class="text-orange-700 font-semibold">
                                <i class="fas fa-calendar mr-2"></i>
                                @if ($combo->start_date && $combo->end_date)
                                    {{ __('Valid from') }} {{ $combo->start_date->format('M d, Y') }}
                                    {{ __('to') }} {{ $combo->end_date->format('M d, Y') }}
                                @elseif ($combo->end_date)
                                    {{ __('Offer ends') }} {{ $combo->end_date->format('M d, Y') }}
                                @elseif ($combo->start_date)
                                    {{ __('Starts') }} {{ $combo->start_date->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Pricing Box -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-500">{{ __('Original Price') }}</span>
                            <span class="text-xl text-gray-400 line-through">{{ number_format($combo->original_price, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-800 font-semibold">{{ __('Combo Price') }}</span>
                            <span class="text-3xl font-bold text-orange-600">{{ number_format($combo->combo_price, 2) }}</span>
                        </div>
                        @if ($combo->savings > 0)
                            <div class="flex items-center justify-between pt-4 border-t">
                                <span class="text-green-600 font-semibold">{{ __('Your Savings') }}</span>
                                <span class="text-xl font-bold text-green-600">{{ number_format($combo->savings, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Included Items -->
            <div class="border-t p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-list-ul text-orange-500 mr-2"></i>
                    {{ __('What\'s Included') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($combo->items as $comboItem)
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            @if ($comboItem->menuItem && $comboItem->menuItem->image)
                                <img src="{{ asset($comboItem->menuItem->image) }}"
                                    alt="{{ $comboItem->menuItem->name }}"
                                    class="w-20 h-20 object-cover rounded-lg mr-4">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-400"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">
                                    {{ $comboItem->menuItem->name ?? __('Item') }}
                                </h3>
                                @if ($comboItem->variant)
                                    <p class="text-sm text-gray-500">{{ $comboItem->variant->name }}</p>
                                @endif
                                <p class="text-orange-600 font-semibold mt-1">
                                    {{ __('Qty') }}: {{ $comboItem->quantity }}
                                </p>
                            </div>
                            @php
                                $itemPrice = ($comboItem->menuItem->base_price ?? 0) + ($comboItem->variant->price_adjustment ?? 0);
                            @endphp
                            <div class="text-right">
                                <p class="text-gray-400 text-sm">{{ __('Value') }}</p>
                                <p class="font-semibold text-gray-700">{{ number_format($itemPrice * $comboItem->quantity, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Back to Menu -->
        <div class="mt-8 text-center">
            <a href="{{ route('website.menu') }}" class="inline-block text-orange-600 hover:text-orange-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to Menu') }}
            </a>
        </div>
    </div>
@endsection
