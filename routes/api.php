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

    Route::get('categories', 'BasicController@getCategories');
    Route::get('account_types', 'BasicController@getAccountTypes');
    Route::get('branches', 'BasicController@getBranches');
    Route::post('password/reset', 'PasswordController@reset');
    Route::post('password/verify', 'PasswordController@verify');


    Route::post('login', 'LoginController@login');
    Route::post('social', 'LoginController@social');
    Route::post('register', 'RegisterController@register');
    Route::post('send_verification_code', 'RegisterController@sendVerificationCode');

    Route::get('setting', 'BasicController@getSettings');

    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('home', 'BasicController@home');
        Route::post('logout', 'UserController@logout');
        Route::post('user/update', 'UserController@update');
        Route::get('get_user', 'UserController@getUser');
        Route::post('rate', 'BasicController@rate');
        Route::get('products', 'ProductsController@index');
        Route::get('products/{id}', 'ProductsController@show');
        Route::get('favorites', 'FavoritesController@index');
        Route::post('favorites', 'FavoritesController@store');
        Route::resource('orders', 'OrdersController');
        Route::resource('contact_messages', 'ContactMessagesController');
        Route::get('notifications', 'NotificationsController@index');
        Route::get('noti_count', 'NotificationsController@getUnReadNoti');
    });
});
