<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::get('/styleguide', function () {
    abort_unless(app()->environment('local', 'testing'), 404);

    return view('styleguide');
});
