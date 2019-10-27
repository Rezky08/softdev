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


Route::get('/customer', 'CustomerLoginController@index');
Route::post('/customer/login', 'CustomerLoginController@store');
Route::post('/customer/register', 'CustomerRegisterController@store');
Route::get('/customer/profile', 'CustomerRegisterController@index');
Route::get('/customer/{customerId}/profile/', 'CustomerRegisterController@show');



Route::group(['middleware' => ['scopes:customer', 'AuthAPI']], function () {
    Route::get('/customer/cart/', 'CustomerCartController@index');
    Route::get('/customer/cart/{cartId}', 'CustomerCartController@show');
    Route::post('/customer/cart/', 'CustomerCartController@store');
    Route::put('/customer/cart/', 'CustomerCartController@update');
    Route::delete('/customer/cart/', 'CustomerCartController@destroy');
});

Route::group(['middleware' => ['scopes:seller', 'AuthAPI']], function () {
    Route::post('/seller/{sellerId}/product', 'SellerProductController@store');
});
Route::get('/seller', 'sellerLoginController@index');
Route::get('/seller/product', 'SellerProductController@index');
Route::post('/seller/login', 'SellerLoginController@store');
Route::post('/seller/register', 'SellerRegisterController@store');
Route::get('/seller/{sellerId}/product/{productId?}', 'SellerProductController@show');
Route::get('/seller/profile', 'SellerRegisterController@index');
Route::get('/seller/{id}/profile/', 'SellerRegisterController@show');

Route::group(['middleware' => ['scopes:seller', 'AuthAPI']], function () {
    Route::get('/apitest', 'TesterController@index');
    Route::get('/apitest/getAccount', 'CustomerRegisterController@index');
});
Route::post('/apitest/login', 'TesterController@login');


Route::get('/paramtest/{param1}/{param2}', 'TesterController@paramTest');
Route::post('/apitest', 'TesterController@readFile');
