@extends('menu::frontend.layouts.menu-layout')

@section('title', $item->name)

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="text-sm">
                <a href="{{ route('menu.index') }}" class="text-gray-500 hover:text-orange-600">{{ __('Menu') }}</a>
                <span class="mx-2 text-gray-400">/</span>
                @if ($item->category)
                    <a href="{{ route('menu.category', $item->category->slug) }}" class="text-gray-500 hover:text-orange-600">
                        {{ $item->category->name }}
                    </a>
                    <span class="mx-2 text-gray-400">/</span>
                @endif
                <span class="text-gray-800">{{ $item->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="lg:flex">
                <!-- Image Section -->
                <div class="lg:w-1/2">
                    @if ($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                            class="w-full h-96 lg:h-full object-cover">
                    @else
                        <div class="w-full h-96 lg:h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-400 text-6xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="lg:w-1/2 p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $item->name }}</h1>
                            @if ($item->category)
                                <span class="text-orange-600 text-sm">{{ $item->category->name }}</span>
                            @endif
                        </div>
                        @if ($item->is_featured)
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-star mr-1"></i> {{ __('Featured') }}
                            </span>
                        @endif
                    </div>

                    <!-- Dietary Info -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if ($item->is_vegetarian)
                            <span class="badge-dietary badge-vegetarian">
                                <i class="fas fa-leaf mr-1"></i> {{ __('Vegetarian') }}
                            </span>
                        @endif
                        @if ($item->is_vegan)
                            <span class="badge-dietary badge-vegan">{{ __('Vegan') }}</span>
                        @endif
                        @if ($item->is_spicy)
                            <span class="badge-dietary badge-spicy">
                                <i class="fas fa-pepper-hot mr-1"></i> {{ __('Spicy') }} ({{ $item->spice_level }}/5)
                            </span>
                        @endif
                        @if ($item->calories)
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $item->calories }} {{ __('cal') }}
                            </span>
                        @endif
                        @if ($item->preparation_time)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-clock mr-1"></i> {{ $item->preparation_time }} min
                            </span>
                        @endif
                    </div>

                    <!-- Description -->
                    @if ($item->short_description)
                        <p class="text-gray-600 mb-4">{{ $item->short_description }}</p>
                    @endif

                    <!-- Price -->
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-orange-600">{{ number_format($item->base_price, 2) }}</span>
                    </div>

                    <!-- Variants -->
                    @if ($item->variants->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Choose Size/Variant') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($item->variants as $variant)
                                    <button class="variant-btn px-4 py-2 border-2 border-gray-200 rounded-lg hover:border-orange-500 transition
                                        {{ $variant->is_default ? 'border-orange-500 bg-orange-50' : '' }}"
                                        data-price="{{ $item->base_price + $variant->price_adjustment }}">
                                        <span class="font-semibold">{{ $variant->name }}</span>
                                        <span class="text-gray-500 text-sm ml-1">
                                            ({{ $variant->price_adjustment >= 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }})
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add-ons -->
                    @if ($item->addons->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Add-ons') }}</h3>
                            <div class="space-y-2">
                                @foreach ($item->addons as $addon)
                                    <label class="flex items-center justify-between p-3 border rounded-lg hover:border-orange-300 cursor-pointer">
                                        <div class="flex items-center">
                                            <input type="checkbox" class="addon-checkbox w-5 h-5 text-orange-600 rounded"
                                                data-price="{{ $addon->price }}"
                                                {{ $addon->pivot->is_required ? 'checked disabled' : '' }}>
                                            <span class="ml-3">
                                                {{ $addon->name }}
                                                @if ($addon->pivot->is_required)
                                                    <span class="text-red-500 text-xs">({{ __('Required') }})</span>
                                                @endif
                                            </span>
                                        </div>
                                        <span class="text-orange-600 font-semibold">+{{ number_format($addon->price, 2) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Allergens -->
                    @if ($item->allergens && count($item->allergens) > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> {{ __('Allergen Information') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($item->allergens as $allergen)
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">
                                        {{ ucfirst($allergen) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Long Description -->
            @if ($item->long_description)
                <div class="border-t p-8">
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('Description') }}</h3>
                    <div class="prose max-w-none text-gray-600">
                        {!! $item->long_description !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Related Items -->
        @if ($relatedItems->count() > 0)
            <section class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('You might also like') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedItems as $related)
                        <a href="{{ route('menu.item', $related->slug) }}" class="block">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden menu-card">
                                @if ($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}"
                                        class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-800 mb-2">{{ $related->name }}</h3>
                                    <span class="text-orange-600 font-bold">{{ number_format($related->base_price, 2) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.variant-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.variant-btn').forEach(b => {
                    b.classList.remove('border-orange-500', 'bg-orange-50');
                    b.classList.add('border-gray-200');
                });
                this.classList.remove('border-gray-200');
                this.classList.add('border-orange-500', 'bg-orange-50');
            });
        });
    </script>
@endpush
