<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ContactInquiryController;
use App\Http\Controllers\PublicWebsiteController;
use Illuminate\Support\Facades\Route;

// Public website routes
Route::get('/', [PublicWebsiteController::class, 'home'])->name('home');
Route::get('/menu', [PublicWebsiteController::class, 'menu'])->name('public.menu');
Route::get('/about', [PublicWebsiteController::class, 'about'])->name('public.about');
Route::get('/contact', [PublicWebsiteController::class, 'contact'])->name('public.contact');
Route::post('/contact', [ContactInquiryController::class, 'store'])->name('public.contact.store');

// Guest routes (unauthenticated)
Route::middleware('guest')->group(function () {
    // Demo Login
    Route::get('/demo-login/{role}', [LoginController::class, 'demo'])
        ->whereIn('role', ['admin', 'customer'])
        ->name('demo.login');

    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
