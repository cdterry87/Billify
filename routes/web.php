<?php

use App\Livewire\ForgotPassword;
use App\Livewire\Login;
use App\Livewire\Home;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Register;
use App\Livewire\ResetPassword;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)
    ->name('login');

Route::get('/register', Register::class)
    ->name('register');

Route::get('/forgot-password', ForgotPassword::class)
    ->name('forgot-password');

Route::get('/reset-password/{token}', ResetPassword::class)
    ->name('reset-password');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->flash('success', 'You have been successfully logged out.');
    return redirect()->route('login');
})->name('logout');

Route::get('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->flash('success', 'You have been successfully logged out.');
    return redirect()->route('login');
});

Route::get('/privacy-policy', PrivacyPolicy::class)
    ->name('privacy-policy');

Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    Route::get('/', Home::class)
        ->name('home');
});
