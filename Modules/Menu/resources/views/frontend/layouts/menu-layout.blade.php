<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Menu') - {{ config('app.name', 'Restaurant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .menu-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .badge-dietary {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 9999px;
        }
        .badge-vegetarian { background-color: #dcfce7; color: #166534; }
        .badge-vegan { background-color: #d1fae5; color: #065f46; }
        .badge-spicy { background-color: #fee2e2; color: #991b1b; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('menu.index') }}" class="flex items-center">
                        <span class="text-2xl font-bold text-orange-600">{{ config('app.name', 'Restaurant') }}</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('menu.index') }}" class="text-gray-600 hover:text-orange-600 px-3 py-2">
                        {{ __('Menu') }}
                    </a>
                    <a href="{{ route('menu.combos') }}" class="text-gray-600 hover:text-orange-600 px-3 py-2">
                        {{ __('Combos') }}
                    </a>
                </div>

                <div class="flex items-center">
                    <form action="{{ route('menu.search') }}" method="GET" class="hidden md:block">
                        <div class="relative">
                            <input type="text" name="q" placeholder="{{ __('Search menu...') }}"
                                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                value="{{ request('q') }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ config('app.name', 'Restaurant') }}</h3>
                    <p class="text-gray-400">{{ __('Delicious food, delivered fresh to your table.') }}</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('menu.index') }}" class="text-gray-400 hover:text-white">{{ __('Menu') }}</a></li>
                        <li><a href="{{ route('menu.combos') }}" class="text-gray-400 hover:text-white">{{ __('Combos') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('Contact') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@restaurant.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Restaurant') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
