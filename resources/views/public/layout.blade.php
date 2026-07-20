<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'MealHQ'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Spectral:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
@php
    $navSolid = ! request()->routeIs('home');
@endphp

<body class="font-sans" x-data="{ open: false, scrolled: {{ $navSolid ? 'true' : 'false' }} }"
      x-init="$nextTick(() => { scrolled = {{ $navSolid ? 'true' : 'false' }} || window.scrollY > 20 })"
      @scroll.window="scrolled = {{ $navSolid ? 'true' : 'false' }} || window.scrollY > 20">
    @php
        $brand = \App\Models\Setting::get('company_name', 'MealHQ');
    @endphp

    <!-- Navigation -->
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-cream-50/95 backdrop-blur border-b border-cream-200 shadow-sm' : 'bg-transparent'">
        <div class="section">
            <div class="flex h-20 items-center justify-between">
                <a href="{{ route('home') }}" class="font-serif text-2xl font-bold transition-colors"
                   :class="scrolled ? 'text-forest-800' : 'text-cream-50'">
                    {{ $brand }}
                </a>

                <div class="hidden md:flex items-center gap-10">
                    @foreach (['home' => 'Home', 'public.menu' => 'Menu', 'public.about' => 'About', 'public.contact' => 'Contact'] as $route => $label)
                        <a href="{{ route($route) }}"
                           class="relative text-sm font-medium tracking-wide transition-colors"
                           x-bind:class="scrolled ? 'text-charcoal-700 hover:text-forest-700' : 'text-cream-100 hover:text-cream-50'">
                            {{ $label }}
                            @if(request()->routeIs($route))
                                <span class="absolute -bottom-1.5 left-0 h-0.5 w-full bg-olive-500"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('admin.login') }}" class="text-sm font-medium transition-colors hover:text-forest-700"
                       :class="scrolled ? 'text-charcoal-700' : 'text-cream-50/90 hover:text-cream-50'">Staff Sign In</a>
                    <a href="{{ route('public.menu') }}" class="btn-primary !px-6 !py-2.5">View Menu</a>
                </div>

                <button @click="open = !open" class="md:hidden inline-flex items-center justify-center rounded-lg p-2 transition-colors"
                        :class="scrolled ? 'text-charcoal-800' : 'text-cream-50'" aria-label="Toggle menu">
                    <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-cloak x-transition class="md:hidden bg-cream-50 border-t border-cream-200">
            <div class="section py-4 flex flex-col gap-1">
                @foreach (['home' => 'Home', 'public.menu' => 'Menu', 'public.about' => 'About', 'public.contact' => 'Contact'] as $route => $label)
                    <a href="{{ route($route) }}" class="rounded-lg px-3 py-2.5 text-charcoal-700 hover:bg-cream-100 {{ request()->routeIs($route) ? 'text-forest-700 font-medium' : '' }}">{{ $label }}</a>
                @endforeach
                <div class="mt-2 flex flex-col gap-2 border-t border-cream-200 pt-3">
                    <a href="{{ route('admin.login') }}" class="rounded-lg px-3 py-2.5 text-charcoal-700 hover:bg-cream-100">Staff Sign In</a>
                    <a href="{{ route('public.menu') }}" class="btn-primary">View Menu</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="{{ $navSolid ? 'pt-20' : '' }}">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-charcoal-900 text-cream-200/70">
        <div class="section py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <div class="md:col-span-1">
                    <h3 class="font-serif text-xl text-cream-50 mb-3">{{ $brand }}</h3>
                    <p class="text-sm leading-relaxed">{{ \App\Models\Setting::get('company_address', '') }}</p>
                    <p class="text-sm mt-2">{{ \App\Models\Setting::get('company_phone', '') }}</p>
                    <p class="text-sm">{{ \App\Models\Setting::get('company_email', '') }}</p>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-olive-400 mb-4">Explore</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-cream-50 transition">Home</a></li>
                        <li><a href="{{ route('public.menu') }}" class="hover:text-cream-50 transition">Menu</a></li>
                        <li><a href="{{ route('public.about') }}" class="hover:text-cream-50 transition">About</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-cream-50 transition">Contact</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-olive-400 mb-4">Opening Hours</h4>
                    @php $hours = \App\Models\BusinessHour::get()->sortBy(function($h) { return array_search($h->day_of_week, ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']); }); @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1.5 text-sm">
                        @foreach($hours as $hour)
                            <div class="flex justify-between border-b border-charcoal-800 py-1">
                                <span class="capitalize">{{ $hour->day_of_week }}</span>
                                <span class="text-cream-50/90">
                                    @if($hour->is_closed)
                                        Closed
                                    @else
                                        {{ \Carbon\Carbon::parse($hour->opening_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($hour->closing_time)->format('g:i A') }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-12 border-t border-charcoal-800 pt-8 text-center text-xs tracking-wide">
                &copy; {{ date('Y') }} {{ $brand }}. All rights reserved.
                <span class="mt-1 block text-cream-200/60">
                    Created with <span class="text-clay-500">&#10084;</span> by
                    <a href="https://rhtech.dev/" target="_blank" rel="noopener" class="font-medium text-cream-100 underline-offset-2 hover:underline">rhtech</a>
                </span>
            </div>
        </div>
    </footer>

    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
