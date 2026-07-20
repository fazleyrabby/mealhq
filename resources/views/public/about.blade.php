@extends('public.layout')

@section('title', 'About Us - ' . \App\Models\Setting::get('company_name', 'MealHQ'))

@section('content')
<section class="bg-cream-100 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="eyebrow">Our Story</span>
            <h1 class="mt-3 font-serif text-4xl text-forest-800 md:text-5xl">About Us</h1>
            <p class="mt-4 text-lg text-charcoal-700/80">Our story and passion for great food</p>
        </div>

        @if($content)
            <div class="prose-about max-w-none text-charcoal-700/85 leading-relaxed">
                {!! $content !!}
            </div>
        @else
            <div class="space-y-10">
                <div class="card-dish p-8">
                    <h2 class="font-serif text-2xl text-forest-800 mb-3">Who We Are</h2>
                    <p class="text-charcoal-700/80 leading-relaxed">
                        {{ \App\Models\Setting::get('company_name', 'MealHQ') }} is a neighborhood restaurant built on a simple belief: great food brings people together. We source the freshest local ingredients and craft every dish by hand.
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div class="card-dish p-6 text-center">
                        <h3 class="font-serif text-xl text-forest-800">Fresh</h3>
                        <p class="mt-2 text-sm text-charcoal-700/75">Locally sourced produce, delivered daily.</p>
                    </div>
                    <div class="card-dish p-6 text-center">
                        <h3 class="font-serif text-xl text-forest-800">Crafted</h3>
                        <p class="mt-2 text-sm text-charcoal-700/75">Every plate prepared by hand with care.</p>
                    </div>
                    <div class="card-dish p-6 text-center">
                        <h3 class="font-serif text-xl text-forest-800">Welcoming</h3>
                        <p class="mt-2 text-sm text-charcoal-700/75">A warm space for every occasion.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
