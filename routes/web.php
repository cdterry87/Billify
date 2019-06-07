<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Laravel default auth routes
Auth::routes();

// Require the auth middleware to hit all these routes
Route::group(['middleware' => 'auth'], function () {

    //Api specific routes
    Route::prefix('api')->group(function () {
        // Primary Resources
        Route::resource('/bills', 'BillController');

        // Logout
        Route::get('/logout', function () {
            Auth::logout();
            return Redirect::to('login');
        });
    });

    // Catch-all routes to route everything back to the home index
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
