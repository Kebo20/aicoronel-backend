<?php

use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::get('providers/list', 'ProviderController@list');
        Route::get('clients/list', 'ClientController@list');
        Route::get('roles/list', 'RoleController@list');
        Route::get('storages/list', 'StorageController@list');
        Route::get('categories/list', 'CategoryController@list');
        Route::get('products/list', 'ProductController@list');
        Route::get('users/list', 'UserController@list');
        Route::get('lots/list', 'LotController@list');
        Route::get('purchases/print/{id}', 'PurchaseController@print');
        Route::get('sales/print/{id}', 'SaleController@print');
        Route::post('purchases/printADate', 'PurchaseController@printADate');
        Route::post('sales/printADate', 'SaleController@printADate');
        Route::post('prueba', 'AuthController@prueba');
        Route::apiResource('providers', 'ProviderController');
        Route::apiResource('clients', 'ClientController');
        Route::apiResource('roles', 'RoleController');
        Route::apiResource('storages', 'StorageController');
        Route::apiResource('categories', 'CategoryController');
        Route::apiResource('products', 'ProductController');
        Route::apiResource('users', 'UserController');
        Route::apiResource('lots', 'LotController');
        Route::apiResource('purchases', 'PurchaseController');
        Route::apiResource('sales', 'SaleController');
    });
});

Route::post('crear', 'Auth\RegisterController@create');
