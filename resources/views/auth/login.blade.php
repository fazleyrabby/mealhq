<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign In - {{ config('app.name', 'MealHQ') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Spectral:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-cream-100">
    <div class="min-h-screen grid lg:grid-cols-2">
        <!-- Brand panel -->
        <div class="relative hidden overflow-hidden bg-forest-800 lg:flex flex-col justify-between p-12 text-cream-50">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(163,160,78,0.25),transparent_55%)]"></div>
            <div class="relative z-10 font-serif text-3xl font-bold">{{ config('app.name', 'MealHQ') }}</div>
            <div class="relative z-10">
                <h2 class="font-serif text-4xl leading-tight">Run your restaurant with ease.</h2>
                <p class="mt-4 max-w-md text-cream-100/80">Sign in to manage your menu, orders, promotions and more from a single dashboard.</p>
            </div>
            <div class="relative z-10 text-sm text-cream-100/60">Created with &#10084; by <a href="https://rhtech.dev/" target="_blank" rel="noopener" class="underline">rhtech</a></div>
        </div>

        <!-- Form panel -->
        <div class="flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-sm">
                <div class="mb-8 lg:hidden">
                    <div class="font-serif text-2xl font-bold text-forest-800">{{ config('app.name', 'MealHQ') }}</div>
                </div>
                <h1 class="font-serif text-3xl text-forest-800">Staff Sign In</h1>
                <p class="mt-2 text-charcoal-700/70">Enter your credentials to access the admin panel.</p>

                @if($errors->any())
                    <div class="mt-6 rounded-lg bg-clay-500/10 border border-clay-500/30 px-4 py-3 text-sm text-clay-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-forest-800">Email</label>
                        <input id="email" type="email" name="email" required autofocus
                               value="{{ old('email') }}"
                               class="w-full rounded-lg border border-cream-300 bg-white px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-forest-800">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full rounded-lg border border-cream-300 bg-white px-4 py-2.5 text-charcoal-800 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-200">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-charcoal-700/80">
                            <input type="checkbox" name="remember" class="rounded border-cream-300 text-forest-700 focus:ring-forest-500"> Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full">Sign In</button>
                </form>

                <div class="mt-6 border-t border-cream-200 pt-6">
                    <a href="{{ route('admin.demo.login') }}" class="btn-outline w-full">Use Demo Admin Account</a>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-charcoal-700/70 hover:text-forest-700">&larr; Back to website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
