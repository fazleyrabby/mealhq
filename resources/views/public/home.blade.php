@extends('public.layout')

@section('title', $companyName . ' - Home')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-orange-50 to-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Welcome to {{ $companyName }}
                </h1>
                <p class="text-lg md:text-xl text-gray-600 mb-8">
                    Experience exceptional cuisine crafted with passion. Fresh ingredients, bold flavors, and unforgettable moments await you.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('public.menu') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition">
                        View Our Menu
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border-2 border-orange-600 text-orange-600 font-medium rounded-lg hover:bg-orange-50 transition">
                        Order Online
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Promotions -->
    @if($promotions->count())
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Current Offers</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($promotions as $promo)
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $promo->title }}</h3>
                    @if($promo->subtitle)
                        <p class="text-gray-600 mb-4">{{ $promo->subtitle }}</p>
                    @endif
                    @if($promo->promo_code)
                        <div class="inline-block bg-orange-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                            Code: {{ $promo->promo_code }}
                        </div>
                    @endif
                    @if($promo->cta_url)
                        <a href="{{ $promo->cta_url }}" class="block mt-4 text-orange-600 font-medium hover:underline">
                            {{ $promo->cta_text ?? 'Learn More' }} &rarr;
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Features -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Ordering</h3>
                    <p class="text-gray-600">Order your favorite meals online with just a few clicks.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Hot and fresh food delivered right to your doorstep.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality Ingredients</h3>
                    <p class="text-gray-600">We use only the freshest ingredients sourced locally.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-orange-600">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to experience amazing food?</h2>
            <p class="text-orange-100 mb-8 text-lg">Reserve your table or order online today.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('public.menu') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 font-medium rounded-lg hover:bg-orange-50 transition">
                    Order Now
                </a>
                <a href="{{ route('public.contact') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-medium rounded-lg hover:bg-orange-700 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
@endsection
