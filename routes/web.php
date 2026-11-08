<?php

use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->middleware('registration.enabled')->name('register');
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset');
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::livewire('/', 'pages::dashboard')->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');

Route::get('/styleguide', function () {
    abort_unless(app()->environment('local', 'testing'), 404);

    return view('styleguide');
});
