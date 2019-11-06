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

// Route Pattern
Route::pattern('id', '[0-9]+');
Route::pattern('sellerId', '[0-9]+');
Route::pattern('customerId', '[0-9]+');
Route::pattern('cartId', '[0-9]+');
Route::pattern('productId', '[0-9]+');
Route::pattern('shopId', '[0-9]+');

//

Route::post('/customer/login', 'CustomerLoginController@store');
Route::post('/customer/register', 'CustomerRegisterController@store');
Route::get('/customer/profile', 'CustomerRegisterController@index');
Route::get('/customer/{customerId}/profile', 'CustomerRegisterController@show');

Route::group(['middleware' => ['checkScopes:customer']], function () {
    Route::post('/customer/purchase', 'CustomerTransactionController@store')->middleware(['cartValidate']);
    Route::get('/customer', 'CustomerLoginController@index');
    Route::get('/customer/cart', 'CustomerCartController@index');
    Route::get('/customer/cart/{cartId}', 'CustomerCartController@show');
    Route::post('/customer/cart', 'CustomerCartController@store');
    Route::put('/customer/cart', 'CustomerCartController@update');
    Route::delete('/customer/cart', 'CustomerCartController@destroy');
    Route::delete('/customer/logout', 'CustomerLoginController@destroy');
});

Route::get('/seller/product', 'SellerProductController@index');
Route::post('/seller/login', 'SellerLoginController@store');
Route::post('/seller/register', 'SellerRegisterController@store');
Route::get('/seller/{shopId}/product', 'SellerProductController@showByShop');
Route::get('/seller/product/{productId}', 'SellerProductController@showById');
Route::get('/seller/profile', 'SellerRegisterController@index');
Route::get('/seller/{id}/profile', 'SellerRegisterController@show');
Route::group(['middleware' => ['checkScopes:seller']], function () {
    Route::get('/seller', 'sellerLoginController@index');
    Route::delete('/seller/logout', 'SellerLoginController@destroy');
    Route::post('/seller/product', 'SellerProductController@store');
    Route::put('/seller/product', 'SellerProductController@update');
    Route::delete('/seller/product', 'SellerProductController@destroy');
});

Route::get('/seller/transaction/{id}', 'SellerTransactionController@show');
Route::get('/test', 'TesterController@checkShop');
