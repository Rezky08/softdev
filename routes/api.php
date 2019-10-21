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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/customer/login', 'CustomerLoginController@store');
// Route::get('/customer/cart', 'CustomerCartController@show');
Route::post('/customer/register', 'CustomerRegisterController@store');
Route::get('/customer/profile', 'CustomerRegisterController@index');
Route::get('/customer/{customerId}/profile/', 'CustomerRegisterController@show');

// Route::group(['middleware' => ['AuthAPI']], function () {
Route::get('/customer/{customerId}/cart/', 'CustomerCartController@index');
Route::get('/customer/{customerId}/cart/{cartId}', 'CustomerCartController@show');
Route::post('/customer/{customerId}/cart/', 'CustomerCartController@store');

Route::post('/seller/{sellerId}/product', 'SellerProductController@store');
// });

Route::get('/seller/product', 'SellerProductController@index');
Route::post('/seller/login', 'SellerLoginController@store');
Route::post('/seller/register', 'SellerRegisterController@store');
Route::get('/seller/{sellerId}/product/{productId?}', 'SellerProductController@show');
Route::get('/seller/profile', 'SellerRegisterController@index');
Route::get('/seller/{id}/profile/', 'SellerRegisterController@show');

Route::group(['middleware' => ['AuthAPI']], function () {
    Route::get('/apitest', 'TesterController@index');
    Route::get('/apitest/2', 'TesterController@JWTTest');
    Route::get('/apitest/getAccount', 'CustomerRegisterController@index');
});

Route::get('/paramtest/{param1}/{param2}', 'TesterController@paramTest');
Route::post('/apitest', 'TesterController@readFile');
