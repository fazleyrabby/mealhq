@extends('public.layout')

@section('title', 'About Us - ' . \App\Models\Setting::get('company_name', 'MealHQ'))

@section('content')
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">About Us</h1>
            <p class="text-lg text-gray-600">Our story and passion for great food</p>
        </div>

        @if($page)
            <div class="prose prose-lg max-w-none">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl text-gray-500 mb-2">About page content coming soon</h3>
                <p class="text-gray-400">We're crafting our story. Check back later!</p>
            </div>
        @endif
    </div>
</section>
@endsection
