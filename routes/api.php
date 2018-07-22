<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::get('user', function (Request $request) {
    return _api_json(false, 'user');
})->middleware('jwt.auth');
Route::group(['namespace' => 'Api'], function () {



    Route::get('token', 'BasicController@getToken');
    Route::get('settings', 'BasicController@getSettings');

    Route::get('vehicle_types', 'BasicController@getVehiclesTypes');
    Route::get('rejection_reasons', 'BasicController@getRejectionReasons');


    Route::get('branches', 'BasicController@getBranches');
    Route::post('password/reset', 'PasswordController@reset');
    Route::post('password/verify', 'PasswordController@verify');


    Route::post('login', 'LoginController@login');
    Route::post('social', 'LoginController@social');
    Route::post('register', 'RegisterController@register');
    Route::post('send_verification_code', 'RegisterController@sendVerificationCode');

    Route::get('setting', 'BasicController@getSettings');
    Route::get('drivers', 'UserController@getNearestDrivers');
    Route::resource('orders', 'OrdersController');
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('home', 'BasicController@home');
        Route::post('logout', 'UserController@logout');
        Route::post('user/update', 'UserController@update');
        Route::post('user/complete_data', 'UserController@complete_data');
        Route::get('auth_user', 'UserController@getAuthUser');
        Route::post('rate', 'BasicController@rate');
        Route::get('products', 'ProductsController@index');
        Route::get('products/{id}', 'ProductsController@show');
        Route::get('favorites', 'FavoritesController@index');
        Route::post('favorites', 'FavoritesController@store');

        Route::resource('contact_messages', 'ContactMessagesController');
        Route::get('notifications', 'NotificationsController@index');
        Route::get('noti_count', 'NotificationsController@getUnReadNoti');
        Route::post('update_location', 'UserController@updateLocation');
        Route::post('change_driver_status', 'UserController@changeDriverStatus');
        Route::get('check_driver_price', 'UserController@checkDriverPrice');
        Route::post('orders/status', 'OrdersController@changeOrderStatus');
        Route::post('orders/rate', 'OrdersController@rate');
    });
});
