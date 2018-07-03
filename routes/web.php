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


$languages = array('ar', 'en', 'fr');
$defaultLanguage = 'ar';
if ($defaultLanguage) {
    $defaultLanguageCode = $defaultLanguage;
} else {
    $defaultLanguageCode = 'ar';
}

$currentLanguageCode = Request::segment(1, $defaultLanguageCode);
if (in_array($currentLanguageCode, $languages)) {
    Route::get('/', function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });


    Route::group(['namespace' => 'Front', 'prefix' => $currentLanguageCode], function () use($currentLanguageCode) {
        app()->setLocale($currentLanguageCode);
        app()->setLocale($currentLanguageCode);
        Route::get('/', 'HomeController@index')->name('home');
        Auth::routes();

   



        /*         * ************************* user ************** */
        Route::group(['namespace' => 'Customer', 'prefix' => 'customer'], function () {
            Route::get('dashboard', 'DashboardController@index');
            Route::get('user/edit', 'UserController@showEditForm');
            Route::post('user/edit', 'UserController@edit');
            Route::get('user/notifications', 'UserController@notifications');
        });
    });
} else {
    Route::get('/' . $currentLanguageCode, function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });
}


//Route::group(['middleware'=>'auth:admin'], function () {
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/error', 'AdminController@error')->name('admin.error');
    Route::get('/change_lang', 'AjaxController@change_lang')->name('ajax.change_lang');

    Route::get('profile', 'ProfileController@index');
    Route::patch('profile', 'ProfileController@update');



    Route::resource('groups', 'GroupsController');
    Route::resource('admins', 'AdminsController');

    Route::resource('categories', 'CategoriesController');
    Route::post('categories/data', 'CategoriesController@data');

    Route::resource('rejection_reasons', 'RejectionReasonsController');
    Route::post('rejection_reasons/data', 'RejectionReasonsController@data');

    Route::resource('branches', 'BranchesController');
    Route::post('branches/data', 'BranchesController@data');
    
    Route::resource('vehicle_weights', 'VehicleWeightsController');
    Route::post('vehicle_weights/data', 'VehicleWeightsController@data');

    Route::resource('clients', 'ClientsController');
    Route::post('clients/data', 'ClientsController@data');
    Route::get('clients/status/{id}', 'ClientsController@status');
    Route::get('products/{id}/gallery', 'ProductsController@gallery');
    Route::post('products/upload', 'ProductsController@upload');
    Route::resource('orders_reports', 'OrdersReportsController');
    Route::post('orders_reports/reply', 'OrdersReportsController@reply');
    Route::get('settings', 'SettingsController@index');






    Route::post('settings', 'SettingsController@store');
    Route::get('notifications', 'NotificationsController@index');
    Route::get('reservations', 'ReservationsController@index');
    Route::post('notifications', 'NotificationsController@store');




    Route::post('groups/data', 'GroupsController@data');
    Route::post('locations/data', 'LocationsController@data');






    Route::post('admins/data', 'AdminsController@data');

    Route::resource('contact_messages', 'ContactMessagesController');
    Route::post('contact_messages/reply', 'ContactMessagesController@reply');
    Route::post('contact_messages/data', 'ContactMessagesController@data');


    $this->get('login', 'LoginController@showLoginForm')->name('admin.login');
    $this->post('login', 'LoginController@login')->name('admin.login.submit');
    $this->get('logout', 'LoginController@logout')->name('admin.logout');
});
//});

