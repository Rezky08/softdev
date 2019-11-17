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

// Route customer
Route::post('/customer/login', 'CustomerLoginController@store');
Route::post('/customer/register', 'CustomerRegisterController@store');
Route::get('/customer/profile', 'CustomerRegisterController@index');
Route::get('/customer/{customerId}/profile', 'CustomerRegisterController@show');

Route::group(['middleware' => ['checkScopes:customer']], function () {
    Route::post('/customer/purchase', 'CustomerSellerTransactionController@store')->middleware(['cartValidate:customer']);
    Route::get('/customer', 'CustomerLoginController@index');
    Route::get('/customer/cart', 'CustomerCartController@index');
    Route::get('/customer/cart/{cartId}', 'CustomerCartController@show');
    Route::post('/customer/cart', 'CustomerCartController@store');
    Route::put('/customer/cart', 'CustomerCartController@update');
    Route::delete('/customer/cart', 'CustomerCartController@destroy');
    Route::delete('/customer/logout', 'CustomerLoginController@destroy');
});
//

// Route seller
Route::post('/seller/login', 'SellerLoginController@store');
Route::post('/seller/register', 'SellerRegisterController@store');
Route::get('/seller/product', 'SellerProductController@index');
Route::get('/seller/{shopId}/product', 'SellerProductController@showByShop');
Route::get('/seller/product/{productId}', 'SellerProductController@showById');
Route::get('/seller/profile', 'SellerRegisterController@index');
Route::get('/seller/{id}/profile', 'SellerRegisterController@show');

Route::group(['middleware' => ['checkScopes:seller']], function () {
    Route::get('/seller/transaction/{id}', 'SellerCustomerTransactionController@show');
    Route::get('/seller/transaction', 'SellerCustomerTransactionController@index');
    Route::post('/seller/purchase', 'SellerSupplierTransactionController@store')->middleware(['cartValidate:seller']);
    Route::get('/seller/cart', 'SellerCartController@index');
    Route::get('/seller/cart/{cartId}', 'SellerCartController@show');
    Route::post('/seller/cart', 'SellerCartController@store');
    Route::put('/seller/cart', 'SellerCartController@update');
    Route::delete('/seller/cart', 'SellerCartController@destroy');
    Route::get('/seller', 'sellerLoginController@index');
    Route::delete('/seller/logout', 'SellerLoginController@destroy');
    Route::post('/seller/product', 'SellerProductController@store');
    Route::put('/seller/product', 'SellerProductController@update');
    Route::delete('/seller/product', 'SellerProductController@destroy');
});
//

// Route Supplier
Route::post('/supplier/login', 'SupplierLoginController@store');
Route::post('/supplier/register', 'SupplierRegisterController@store');
Route::get('/supplier/product', 'SupplierProductController@index');
Route::get('/supplier/{shopId}/product', 'SupplierProductController@showByShop');
Route::get('/supplier/product/{productId}', 'SupplierProductController@showById');

Route::group(['middleware' => ['checkScopes:supplier']], function () {
    Route::get('/supplier', 'SupplierLoginController@index');
    Route::delete('/supplier/logout', 'SupplierLoginController@destroy');
    Route::post('/supplier/product', 'SupplierProductController@store');
    Route::put('/supplier/product', 'SupplierProductController@update');
    Route::delete('/supplier/product', 'SupplierProductController@destroy');
});
//
Route::get('/test', 'TesterController@checkShop');
Route::get('/test/supplier', 'TesterController@supplierCheck');
Route::post('/test/sendbalance', 'CoinTransactionController@store');
