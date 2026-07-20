<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'MealHQ'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold text-orange-600">
                    {{ \App\Models\Setting::get('company_name', 'MealHQ') }}
                </a>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-orange-600 transition">Home</a>
                    <a href="{{ route('public.menu') }}" class="text-gray-600 hover:text-orange-600 transition">Menu</a>
                    <a href="{{ route('public.about') }}" class="text-gray-600 hover:text-orange-600 transition">About</a>
                    <a href="{{ route('public.contact') }}" class="text-gray-600 hover:text-orange-600 transition">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-600 transition">Sign In</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-semibold mb-3">{{ \App\Models\Setting::get('company_name', 'MealHQ') }}</h3>
                    <p class="text-sm">{{ \App\Models\Setting::get('company_address', '') }}</p>
                    <p class="text-sm">{{ \App\Models\Setting::get('company_phone', '') }}</p>
                    <p class="text-sm">{{ \App\Models\Setting::get('company_email', '') }}</p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">Hours</h3>
                    @php $hours = \App\Models\BusinessHour::get()->sortBy(function($h) { return array_search($h->day_of_week, ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']); }); @endphp
                    @foreach($hours as $hour)
                        <p class="text-sm capitalize">
                            {{ $hour->day_of_week }}:
                            @if($hour->is_closed)
                                Closed
                            @else
                                {{ \Carbon\Carbon::parse($hour->opening_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($hour->closing_time)->format('g:i A') }}
                            @endif
                        </p>
                    @endforeach
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.menu') }}" class="hover:text-white transition">Menu</a></li>
                        <li><a href="{{ route('public.about') }}" class="hover:text-white transition">About</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name', 'MealHQ') }}. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
