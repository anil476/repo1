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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

 /*
 -------------------below routes for gofuel app--------------------------
*/
// no need to authenticate following api's
Route::post('/api/user/register','RegisterController@registerUsers');
Route::post('/api/user/login','LoginController@login');
Route::get('/api/user/verify/{user_id?}/{otp?}/{mobile_number?}','RegisterController@getVerified');
Route::get('/api/user/resendcode/{user_id?}','RegisterController@resendCode');
Route::post('/api/user/forgotpassword','RegisterController@forgotPassword');
Route::post('/api/user/setnewpassword/{user_id?}','RegisterController@setNewPassword');

// below route use for test purpose only to schedule cron.
Route::get('/api/iocl','CronJobController@postFuels');

// API's that need to be authenticate
Route::group(['middleware' => 'jwt-auth'], function () {

      // vehicle routes
    	Route::post('/api/vehicle','VehicleController@registerVehicles');
      Route::get('/api/vehicle','VehicleController@getVehicleDetails');
      Route::get('/api/vehicle/type','VehicleController@getVehicleType');
      Route::post('/api/vehicle/{vehicle_id?}','VehicleController@deleteVehicle');

      // vehicle activity routes
      Route::post('/api/vehicle/activity/{vehicle_id?}/{category_id?}','ActivityController@registerActivity');
      Route::get('/api/vehicle/activities/categories','ActivityController@getActivityCategories');
      Route::get('/api/vehicle/activities','ActivityController@getActivities');
      Route::get('/api/vehicle/average','VehicleController@getVehicleAverage');

      // vehicle actions routes
      Route::post('/api/user/vehicle/actions','ActionController@postActions');
      Route::get('/api/vehicle/clients/{company?}','ClientController@getClients');
      Route::get('/api/vehicle/locations','ClientController@getLocations');

      // history routes
      Route::get('/api/vehicle/history','VehicleController@getHistory');
      Route::get('/api/dashboard/history','VehicleController@getDashboardHistory');

      // common API's for user and admin and routes for access fuel's info
      Route::get('/api/fuel/fuels','FuelController@getFuels');
      Route::get('/api/fuel/price','FuelController@getFuelPrice');
      Route::get('/api/fuel/cities/{city?}','CityController@getCities');
      Route::get('/api/fuel/states','CityController@getStates');
      Route::get('/api/fuel/categories','FuelController@getFuelCategories');
      Route::get('/api/fuel/companies','FuelController@getCompanies');

      // admin API's perform after login
      Route::post('/api/admin/fuel/{city_id?}','AdminController@postFuel');
      Route::get('/api/user','AdminController@getUser');
      Route::put('/api/user','AdminController@editUser');
      Route::delete('/api/user/{user_id?}','AdminController@deleteUser');
      Route::post('/api/fuel/price','FuelController@postFuel');

      // logout route
      Route::get('/api/user/logout','LoginController@logout');

    });
