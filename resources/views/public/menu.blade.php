@extends('public.layout')

@section('title', 'Menu - ' . \App\Models\Setting::get('company_name', 'MealHQ'))

@section('content')
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Menu</h1>
            <p class="text-lg text-gray-600">Explore our carefully curated selection of dishes</p>
        </div>

        <!-- Menu Placeholder -->
        <div class="text-center py-20">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-xl text-gray-500 mb-2">Menu items coming soon</h3>
            <p class="text-gray-400">We're preparing our menu. Check back shortly!</p>
        </div>
    </div>
</section>
@endsection
