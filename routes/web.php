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
        Route::get('/allbills', 'BillController@all');

        // Update income
        Route::post('/income', 'UserController@income');

        // Get User info
        Route::get('/user', 'UserController@index');

        // Update user account
        Route::post('/account', 'UserController@account');
        Route::post('/password', 'UserController@password');

        // Charts
        Route::get('/charts/incomevspayments', 'ChartController@incomevspayments');
        Route::get('/charts/paymentcategories', 'ChartController@paymentcategories');
        Route::get('/charts/daily', 'ChartController@daily');

        // Notifications
        Route::get('/notifications/', 'HomeController@notifications');

        // Logout
        Route::get('/logout', function () {
            Auth::logout();
            return Redirect::to('login');
        });
    });

    // Catch-all routes to route everything back to the home index
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
