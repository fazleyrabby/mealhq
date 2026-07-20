@extends('public.layout')

@section('title', 'Menu - ' . \App\Models\Setting::get('company_name', 'MealHQ'))

@section('content')
<section class="bg-cream-50 py-20">
    <div class="section">
        <div class="text-center">
            <span class="eyebrow">Our Selection</span>
            <h1 class="mt-3 font-serif text-4xl text-forest-800 md:text-5xl">Our Menu</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-charcoal-700/80">Explore our carefully curated selection of dishes, prepared with the freshest ingredients.</p>
        </div>

        @if($categories->isEmpty())
            <div class="card-dish mt-16 p-12 text-center">
                <h3 class="font-serif text-2xl text-forest-800">Menu items coming soon</h3>
                <p class="mt-2 text-charcoal-700/70">We're preparing our menu. Check back shortly!</p>
            </div>
        @else
            <div class="mt-16 space-y-16">
                @foreach($categories as $category)
                    <div id="{{ Str::slug($category->name) }}">
                        <div class="mb-8 flex items-end justify-between border-b border-cream-200 pb-4">
                            <div>
                                <h2 class="font-serif text-3xl text-forest-800">{{ $category->name }}</h2>
                                @if($category->description)
                                    <p class="mt-1 text-charcoal-700/70">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($category->menuItems as $item)
                                <article class="card-dish flex flex-col">
                                    <div class="aspect-[4/3] w-full overflow-hidden bg-cream-100">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-forest-300">
                                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-1 flex-col p-6">
                                        <div class="flex items-start justify-between gap-3">
                                            <h3 class="font-serif text-xl text-forest-800">{{ $item->name }}</h3>
                                            <div class="text-right">
                                                @if($item->isOnSpecial())
                                                    <span class="block text-xs font-medium text-charcoal-700/50 line-through">${{ number_format($item->base_price, 2) }}</span>
                                                    <span class="whitespace-nowrap font-semibold text-clay-500">${{ number_format($item->effectivePrice(), 2) }}</span>
                                                @else
                                                    <span class="whitespace-nowrap font-semibold text-clay-500">${{ number_format($item->base_price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($item->description)
                                            <p class="mt-2 text-sm leading-relaxed text-charcoal-700/75">{{ $item->description }}</p>
                                        @endif
                                        <div class="mt-auto flex items-center gap-3 pt-5">
                                            @if($item->is_featured)
                                                <span class="rounded-full bg-olive-500/15 px-3 py-1 text-xs font-medium text-olive-600">Featured</span>
                                            @endif
                                            @if($item->isOnSpecial())
                                                <span class="rounded-full bg-clay-500/15 px-3 py-1 text-xs font-medium text-clay-600">Save {{ $item->discountPercent() }}%</span>
                                            @endif
                                            @if($item->calories)
                                                <span class="text-xs text-charcoal-700/55">{{ $item->calories }} cal</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
