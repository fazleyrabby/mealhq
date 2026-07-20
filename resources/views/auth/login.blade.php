@extends($isAdmin ?? false ? 'admin.layout' : 'layouts.auth')

@section('title', 'Login')

@if($isAdmin ?? false)
    @section('content')
    <div class="container-tight py-6">
        <div class="text-center mb-4">
            <h1 class="h2">MealHQ Admin</h1>
            <p class="text-secondary">Sign in to your admin account</p>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@example.com" autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember">
                            <span class="form-check-label">Remember me</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            <div class="btn-list">
                <a href="{{ route('admin.demo.login', 'admin') }}" class="btn btn-amber">Demo Admin</a>
                <a href="{{ route('admin.demo.login', 'customer') }}" class="btn btn-blue">Demo Customer</a>
            </div>
        </div>
    </div>
    @endsection
@else
    @section('content')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('email') border-red-500 @enderror"
                            placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ old('remember') ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Sign in
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-500 mb-3">Quick Demo Access</p>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.demo.login', 'admin') }}"
                           class="flex-1 text-center px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition-colors">
                            Demo Admin
                        </a>
                        <a href="{{ route('admin.demo.login', 'customer') }}"
                           class="flex-1 text-center px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                            Demo Customer
                        </a>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-medium">Create one</a>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        <a href="{{ route('admin.login') }}" class="text-gray-600 hover:text-gray-800">Admin Login &rarr;</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endsection
@endif
