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
Route::get('/customer/cart', 'CustomerCartController@show');
Route::post('/customer/register', 'CustomerRegisterController@store');

Route::post('/seller/login', 'SellerLoginController@store');
Route::post('/seller/register', 'SellerRegisterController@store');

Route::get('/apitest', 'TesterController@index');
Route::post('/apitest', 'TesterController@readFile');
