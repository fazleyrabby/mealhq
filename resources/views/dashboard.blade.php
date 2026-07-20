@extends('layouts.auth')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-900">{{ config('app.name') }}</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
            <p class="text-gray-600">Welcome to {{ config('app.name') }}. You are logged in!</p>
        </div>
    </main>
</div>
@endsection
