@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
            <p class="mt-2 text-sm text-gray-600">Reset your password</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-sm text-gray-600 mb-6">
                Enter your email address and we'll send you a link to reset your password.
            </p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Send reset link
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Remember your password?
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-medium">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
