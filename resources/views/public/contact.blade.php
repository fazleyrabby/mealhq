@extends('public.layout')

@php
    $address = \App\Models\Setting::get('company_address', '123 Main St, City');
    $mapQuery = urlencode($address);
    $mapSrc = 'https://maps.google.com/maps?q=' . $mapQuery . '&z=15&output=embed';
    $hours = \App\Models\BusinessHour::orderByRaw("FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->get();
@endphp

@section('title', 'Contact Us - ' . \App\Models\Setting::get('company_name', 'MealHQ'))

@section('content')
<section class="bg-cream-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="eyebrow">Get In Touch</span>
            <h1 class="mt-3 font-serif text-4xl text-forest-800 md:text-5xl">Contact Us</h1>
            <p class="mt-4 text-lg text-charcoal-700/80">We'd love to hear from you</p>
        </div>

        <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
            <!-- Contact Info -->
            <div>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-forest-800">Address</h3>
                            <p class="text-charcoal-700/80">{{ $address }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-forest-800">Email</h3>
                            <p class="text-charcoal-700/80">{{ \App\Models\Setting::get('company_email', 'info@restaurant.test') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-forest-800">Phone</h3>
                            <p class="text-charcoal-700/80">{{ \App\Models\Setting::get('company_phone', '+1234567890') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-forest-800">Opening Hours</h3>
                            <ul class="text-sm text-charcoal-700/80 space-y-0.5">
                                @forelse($hours as $hour)
                                    <li class="flex justify-between gap-6">
                                        <span class="capitalize">{{ $hour->day_of_week }}</span>
                                        <span>
                                            @if($hour->is_closed)
                                                <span class="text-charcoal-700/50">Closed</span>
                                            @else
                                                {{ \Carbon\Carbon::parse($hour->opening_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($hour->closing_time)->format('g:i A') }}
                                            @endif
                                        </span>
                                    </li>
                                @empty
                                    <li class="text-charcoal-700/50">Mon–Sun, 9:00 AM – 10:00 PM</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="mt-8 overflow-hidden rounded-xl border border-cream-200 shadow-sm">
                    <iframe
                        title="Restaurant location"
                        src="{{ $mapSrc }}"
                        class="h-72 w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" rel="noopener"
                   class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-forest-700 hover:text-olive-600 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Get directions
                </a>
            </div>

            <!-- Contact Form -->
            <div class="card-dish p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-forest-100 p-4 text-forest-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.contact.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-forest-800">Name *</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                    class="w-full rounded-lg border border-cream-300 bg-cream-50 px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                            @error('name')<p class="mt-1 text-xs text-clay-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-forest-800">Email *</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                    class="w-full rounded-lg border border-cream-300 bg-cream-50 px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                            @error('email')<p class="mt-1 text-xs text-clay-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1 block text-sm font-medium text-forest-800">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="w-full rounded-lg border border-cream-300 bg-cream-50 px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                        </div>
                        <div>
                            <label for="subject" class="mb-1 block text-sm font-medium text-forest-800">Subject *</label>
                            <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                                    class="w-full rounded-lg border border-cream-300 bg-cream-50 px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                            @error('subject')<p class="mt-1 text-xs text-clay-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="message" class="mb-1 block text-sm font-medium text-forest-800">Message *</label>
                            <textarea name="message" id="message" rows="4" required
                                      class="w-full rounded-lg border border-cream-300 bg-cream-50 px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-clay-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-primary w-full">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
