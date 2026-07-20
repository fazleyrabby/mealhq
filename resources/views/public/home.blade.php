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

    @if($specials->isNotEmpty())
    <!-- Special Offers -->
    <section class="bg-cream-50 py-20">
        <div class="section">
            <div class="text-center">
                <span class="eyebrow">Limited Time</span>
                <h2 class="mt-3 font-serif text-3xl text-forest-800 md:text-4xl">Special Offers</h2>
                <p class="mx-auto mt-3 max-w-xl text-charcoal-700/80">Hand-picked dishes at a special price — for a limited time only.</p>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($specials as $item)
                    <article class="card-dish flex flex-col overflow-hidden">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-cream-100">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-forest-300">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                </div>
                            @endif
                            <span class="absolute left-4 top-4 rounded-full bg-clay-500 px-3 py-1 text-xs font-semibold text-cream-50">Save {{ $item->discountPercent() }}%</span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="font-serif text-xl text-forest-800">{{ $item->name }}</h3>
                            @if($item->description)
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-charcoal-700/75">{{ $item->description }}</p>
                            @endif
                            <div class="mt-auto flex items-center gap-3 pt-5">
                                <span class="text-sm text-charcoal-700/50 line-through">${{ number_format($item->base_price, 2) }}</span>
                                <span class="font-semibold text-clay-500">${{ number_format($item->effectivePrice(), 2) }}</span>
                                <a href="{{ route('public.menu') }}" class="btn-outline ml-auto !px-5 !py-2 text-sm">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
