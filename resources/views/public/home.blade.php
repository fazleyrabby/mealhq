@extends('public.layout')

@section('title', $companyName . ' - Home')

@section('content')
    <!-- Hero Slider -->
    <section class="relative h-[64vh] min-h-[440px] w-full overflow-hidden bg-charcoal-900"
             x-data="{ active: 0, count: {{ count($slides) }}, timer: null }"
             x-init="timer = setInterval(() => active = (active + 1) % count, 6000)"
             @mouseenter="clearInterval(timer)"
             @mouseleave="timer = setInterval(() => active = (active + 1) % count, 6000)">
        @foreach($slides as $i => $slide)
            <div class="absolute inset-0"
                 x-show="active === {{ $i }}"
                 x-transition.opacity.duration.1000ms
                 style="display: none;">
                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-charcoal-900/45"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-charcoal-900/85 via-charcoal-900/45 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-charcoal-900/80 via-transparent to-charcoal-900/30"></div>
            </div>
        @endforeach

        <div class="relative z-10 flex h-full items-center"
             x-data="{ slides: [
                @foreach($slides as $slide)
                    { title: @js($slide['title']), subtitle: @js($slide['subtitle']), ctaText: @js($slide['cta_text']), ctaUrl: @js($slide['cta_url']) }@if(!$loop->last),@endif
                @endforeach
             ] }">
            <div class="section w-full">
                <div class="max-w-2xl transition-all duration-700 ease-in-out">
                    <span class="eyebrow text-cream-100/90">Welcome to {{ $companyName }}</span>
                    <h1 class="mt-4 font-serif text-4xl leading-tight text-cream-50 drop-shadow-sm sm:text-5xl md:text-6xl" x-text="slides[active].title"></h1>
                    <p class="mt-5 max-w-xl text-lg text-cream-50/90 drop-shadow-sm" x-text="slides[active].subtitle"></p>
                    <div class="mt-8 flex flex-wrap gap-4" x-show="slides[active].ctaText">
                        <a :href="slides[active].ctaUrl || '{{ route('public.menu') }}'" class="btn-accent" x-text="slides[active].ctaText"></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 gap-2.5">
            <template x-for="(b, idx) in Array.from({length: count})" :key="idx">
                <button @click="active = idx" class="h-2 rounded-full transition-all duration-300"
                        :class="active === idx ? 'w-8 bg-gold-400' : 'w-2 bg-cream-50/40 hover:bg-cream-50/70'"></button>
            </template>
        </div>
    </section>

    <!-- Promotions -->
    @if($promotions->count())
    <section class="section py-20">
        <div class="text-center">
            <span class="eyebrow">Limited Time</span>
            <h2 class="mt-3 font-serif text-3xl text-forest-800 md:text-4xl">Current Offers</h2>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach($promotions as $promo)
            <div class="card-dish flex flex-col p-8">
                <h3 class="font-serif text-2xl text-forest-800">{{ $promo->title }}</h3>
                @if($promo->subtitle)
                    <p class="mt-2 text-charcoal-700/80">{{ $promo->subtitle }}</p>
                @endif
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-semibold text-clay-500">
                        {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : '$' . number_format($promo->discount_value, 2) }}
                    </span>
                    <span class="text-sm uppercase tracking-wide text-charcoal-700/60">Off</span>
                </div>
                @if($promo->promo_code)
                    <div class="mt-4 inline-flex w-fit items-center gap-2 rounded-full bg-cream-200 px-4 py-1.5 text-sm font-medium text-forest-800">
                        Code: <span class="font-semibold tracking-wider">{{ $promo->promo_code }}</span>
                    </div>
                @endif
                @if($promo->cta_url)
                    <a href="{{ $promo->cta_url }}" class="mt-5 font-medium text-forest-700 hover:text-clay-500">
                        {{ $promo->cta_text ?? 'Learn More' }} &rarr;
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Features -->
    <section class="bg-cream-100 py-20">
        <div class="section">
            <div class="grid grid-cols-1 gap-10 text-center md:grid-cols-3">
                <div class="p-4">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="font-serif text-xl text-forest-800">Easy Ordering</h3>
                    <p class="mt-2 text-charcoal-700/80">Order your favorite meals online with just a few clicks.</p>
                </div>
                <div class="p-4">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-serif text-xl text-forest-800">Fast Delivery</h3>
                    <p class="mt-2 text-charcoal-700/80">Hot and fresh food delivered right to your doorstep.</p>
                </div>
                <div class="p-4">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h3 class="font-serif text-xl text-forest-800">Quality Ingredients</h3>
                    <p class="mt-2 text-charcoal-700/80">We use only the freshest ingredients sourced locally.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-forest-800 py-20">
        <div class="section text-center">
            <h2 class="font-serif text-3xl text-cream-50 md:text-4xl">Ready to experience amazing food?</h2>
            <p class="mt-3 text-lg text-cream-100/80">Reserve your table or order online today.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('public.menu') }}" class="btn-accent">Order Now</a>
                <a href="{{ route('public.contact') }}" class="btn-ghost-light">Contact Us</a>
            </div>
        </div>
    </section>
@endsection
