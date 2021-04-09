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
    ], function () {
        Route::get('logout', 'AuthController@logout');

        Route::group([
            'middleware' => 'admin'
        ], function () {
            Route::get('user', 'AuthController@user');
            Route::get('providers/list', 'ProviderController@list');
            Route::get('roles/list', 'RoleController@list');
            Route::get('storages/list', 'StorageController@list');
            Route::get('categories/list', 'CategoryController@list');
            Route::get('users/list', 'UserController@list');

            Route::apiResource('providers', 'ProviderController');
            Route::apiResource('roles', 'RoleController');
            Route::apiResource('storages', 'StorageController');
            Route::apiResource('categories', 'CategoryController');
            Route::apiResource('products', 'ProductController');
            Route::apiResource('users', 'UserController');
            Route::apiResource('purchases', 'PurchaseController');

            Route::post('lots/add', 'LotController@add');
            Route::post('lots/remove', 'LotController@remove');

            Route::get('lots/moves-product-initial/{id}', 'LotController@listMovementInitialByStorage');
        });


        Route::get('products', 'ProductController@index');
        Route::get('providers', 'ProviderController@index');
        Route::get('roles', 'RoleController@index');
        Route::get('storages', 'StorageController@index');
        Route::get('categories', 'CategoryController@index');
        Route::get('products', 'ProductController@index');

        Route::post('prueba', 'AuthController@prueba');
        Route::post('lots/list', 'LotController@list');
        Route::get('clients/list', 'ClientController@list');
        Route::apiResource('clients', 'ClientController');
        Route::apiResource('lots', 'LotController');
        Route::apiResource('sales', 'SaleController');

        Route::get('purchases/print/{id}', 'PurchaseController@print');
        Route::post('purchases/correlativo', 'PurchaseController@correlativo');
        Route::get('purchases/export/{id}', 'PurchaseController@export');

        Route::post('sales/correlativo', 'SaleController@correlativo');

        Route::get('sales/print/{id}', 'SaleController@print');
        Route::get('sales/export/{id}', 'SaleController@export');
        Route::post('purchases/printADate', 'PurchaseController@printADate');
        Route::post('purchases/printDateToDate', 'PurchaseController@printDateToDate');
        Route::post('purchases/printForMonth', 'PurchaseController@printForMonth');
        Route::post('purchases/exportADate', 'PurchaseController@exportADate');
        Route::post('purchases/exportDateToDate', 'PurchaseController@exportDateToDate');
        Route::post('purchases/exportForMonth', 'PurchaseController@exportForMonth');
        Route::post('sales/printADate', 'SaleController@printADate');
        Route::post('sales/printDateToDate', 'SaleController@printDateToDate');
        Route::post('sales/printForMonth', 'SaleController@printForMonth');
        Route::post('sales/exportADate', 'SaleController@exportADate');
        Route::post('sales/exportDateToDate', 'SaleController@exportDateToDate');
        Route::post('sales/exportForMonth', 'SaleController@exportForMonth');


        Route::post('providers/count', 'ProviderController@count');
        Route::post('clients/count', 'ClientController@count');
        Route::post('purchases/count', 'PurchaseController@count');
        Route::post('sales/count', 'SaleController@count');

        Route::post('purchases/totalForMonth', 'PurchaseController@totalForMonth');
        Route::post('sales/totalForMonth', 'SaleController@totalForMonth');
    });
});

Route::post('crear', 'Auth\RegisterController@create');
