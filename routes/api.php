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
Route::post('/customer/login', 'Customer\CustomerLoginController@store');
Route::post('/customer/register', 'Integrated\CustomerRegisterController@store');
Route::get('/customer/profile', 'Customer\CustomerRegisterController@index');
Route::get('/customer/{customerId}/profile', 'Customer\CustomerRegisterController@show');

Route::group(['middleware' => ['checkScopes:customer']], function () {
    Route::post('/customer/purchase', 'Integrated\CustomerTransactionController@store')->middleware(['cartValidate:customer']);
    Route::get('/customer', 'Customer\CustomerLoginController@index');
    Route::get('/customer/cart', 'Customer\CustomerCartController@index');
    Route::get('/customer/cart/{cartId}', 'Customer\CustomerCartController@show');
    Route::post('/customer/cart', 'Customer\CustomerCartController@store');
    Route::put('/customer/cart', 'Customer\CustomerCartController@update');
    Route::delete('/customer/cart', 'Customer\CustomerCartController@destroy');
    Route::delete('/customer/logout', 'Customer\CustomerLoginController@destroy');
});
//

// Route seller
Route::post('/seller/login', 'Seller\SellerLoginController@store');
Route::post('/seller/register', 'Integrated\SellerRegisterController@store');
Route::get('/seller/product', 'Seller\SellerProductController@index');
Route::get('/seller/{shopId}/product', 'Seller\SellerProductController@showByShop');
Route::get('/seller/product/{productId}', 'Seller\SellerProductController@showById');
Route::get('/seller/profile', 'Seller\SellerRegisterController@index');
Route::get('/seller/{id}/profile', 'Seller\SellerRegisterController@show');

Route::group(['middleware' => ['checkScopes:seller']], function () {
    Route::get('/seller/transaction/{id}', 'Seller\SellerCustomerTransactionController@show');
    Route::get('/seller/transaction', 'Seller\SellerCustomerTransactionController@index');
    Route::post('/seller/purchase', 'Integrated\SellerTransactionController@store')->middleware(['cartValidate:seller']);
    Route::get('/seller/cart', 'Seller\SellerCartController@index');
    Route::get('/seller/cart/{cartId}', 'Seller\SellerCartController@show');
    Route::post('/seller/cart', 'Seller\SellerCartController@store');
    Route::put('/seller/cart', 'Seller\SellerCartController@update');
    Route::delete('/seller/cart', 'Seller\SellerCartController@destroy');
    Route::get('/seller', 'Seller\sellerLoginController@index');
    Route::delete('/seller/logout', 'Seller\SellerLoginController@destroy');
    Route::post('/seller/product', 'Seller\SellerProductController@store');
    Route::put('/seller/product', 'Seller\SellerProductController@update');
    Route::delete('/seller/product', 'Seller\SellerProductController@destroy');
});
//

// Route Supplier
Route::post('/supplier/login', 'Supplier\SupplierLoginController@store');
Route::post('/supplier/register', 'Integrated\SupplierRegisterController@store');
Route::get('/supplier/product', 'Supplier\SupplierProductController@index');
Route::get('/supplier/{shopId}/product', 'Supplier\SupplierProductController@showByShop');
Route::get('/supplier/product/{productId}', 'Supplier\SupplierProductController@showById');
Route::get('/supplier/{id}/profile', 'Supplier\SupplierRegisterController@show');

Route::group(['middleware' => ['checkScopes:supplier']], function () {
    Route::get('/supplier', 'Supplier\SupplierLoginController@index');
    Route::delete('/supplier/logout', 'Supplier\SupplierLoginController@destroy');
    Route::post('/supplier/product', 'Supplier\SupplierProductController@store');
    Route::put('/supplier/product', 'Supplier\SupplierProductController@update');
    Route::delete('/supplier/product', 'Supplier\SupplierProductController@destroy');
});
//

Route::post('/coin/topup', 'Coin\CoinBalanceController@coinTopUp');
Route::get('/test', 'TesterController@checkShop');
Route::get('/test/supplier', 'TesterController@supplierCheck');
Route::post('/test/sendbalance', 'CoinTransactionController@store');
