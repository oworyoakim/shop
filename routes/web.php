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

use Illuminate\Support\Facades\Route;

Route::get('/login', 'AccountController@login')->name('login');
Route::post('/login', 'AccountController@processLogin')->middleware(['ensure.ajax.request'])->name('login');

Route::group(['middleware' => ['ensure.authenticated']], function () {
    Route::get('/force-password-change', 'AccountController@forcePasswordChange')->name('force-password-change');

    Route::group(['middleware' => ['enforce.password.change']], function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::group(['middleware' => 'ensure.ajax.request'], function () {
            Route::post('/logout', 'AccountController@logout')->name('logout');
            Route::get('/user-data', 'HomeController@getUserData')->name('user-data');
            Route::get('/form-selection-options', 'HomeController@getFormSelectionOptions')->name('form-selection-options');
            Route::get('/salable-products', 'HomeController@getSalableProducts')->name('salable-products');
            Route::get('/purchasable-products', 'HomeController@getPurchasableProducts')->name('purchasable-products');
            Route::get('/get-item-by-barcode', 'HomeController@getItemByBarcode')->name('get-item-by-barcode');
            Route::get('/countries', 'HomeController@getCountries')->name('countries');

            Route::group(['prefix' => 'units'], function () {
                Route::get('', 'UnitsController@index')->name('units');
                Route::post('', 'UnitsController@store')->name('units.create');
                Route::put('', 'UnitsController@update')->name('units.update');
                Route::delete('', 'UnitsController@delete')->name('units.delete');
            });

            Route::group(['prefix' => 'suppliers'], function () {
                Route::get('', 'SuppliersController@index')->name('suppliers');
                Route::post('', 'SuppliersController@store')->name('suppliers.create');
                Route::put('', 'SuppliersController@update')->name('suppliers.update');
                Route::delete('', 'SuppliersController@delete')->name('suppliers.delete');
            });

            Route::group(['prefix' => 'categories'], function () {
                Route::get('', 'CategoriesController@index')->name('categories');
                Route::post('', 'CategoriesController@store')->name('categories.create');
                Route::put('', 'CategoriesController@update')->name('categories.update');
                Route::delete('', 'CategoriesController@delete')->name('categories.delete');
            });

            Route::group(['prefix' => 'items'], function () {
                Route::get('', 'ItemsController@index')->name('items');
                Route::post('', 'ItemsController@store')->name('items.create');
                Route::put('', 'ItemsController@update')->name('items.update');
                Route::delete('', 'ItemsController@delete')->name('items.delete');
                Route::get('print-barcode', 'ItemsController@printBarcode')->name('items.print_barcode');
            });

            Route::group(['prefix' => 'branches'], function () {
                Route::get('', 'BranchesController@index')->name('branches');
                Route::post('', 'BranchesController@store')->name('branches.create');
                Route::put('', 'BranchesController@update')->name('branches.update');
                Route::delete('', 'BranchesController@delete')->name('branches.delete');
                Route::patch('lock', 'BranchesController@lock')->name('branches.lock');
                Route::patch('unlock', 'BranchesController@unlock')->name('branches.unlock');
            });

            Route::group(['prefix' => 'users'], function () {
                Route::get('', 'UsersController@index')->name('users');
                Route::post('', 'UsersController@store')->name('users.create');
                Route::put('', 'UsersController@update')->name('users.update');
                Route::delete('', 'UsersController@delete')->name('users.delete');
                Route::patch('lock', 'UsersController@lock')->name('users.lock');
                Route::patch('unlock', 'UsersController@unlock')->name('users.unlock');
            });

            Route::group(['prefix' => 'roles'], function () {
                Route::get('', 'RolesController@index')->name('roles');
                Route::post('', 'RolesController@store')->name('roles.create');
                Route::put('', 'RolesController@update')->name('roles.update');
                Route::patch('', 'RolesController@updatePermissions')->name('roles.update_permissions');
                Route::delete('', 'RolesController@delete')->name('roles.delete');
            });

            Route::group(['prefix' => 'pos'], function () {
                Route::get('shop-info', 'CashierController@getShopInfo')->name('pos');
                Route::post('complete-sale-transaction', 'StoreSaleController')->name('pos.create_sale');
                Route::patch('cancel-sale-transaction', 'CashierController@cancelSaleTransaction')->name('pos.cancel_sale');
            });

            Route::group(['prefix' => 'stores'], function () {
                Route::post('complete-purchase-transaction', 'StorePurchaseController')->name('pos.create_purchase');
                Route::patch('cancel-purchase-transaction', 'CashierController@cancelPurchaseTransaction')->name('pos.cancel_purchase');
            });


        });

        Route::get('{any?}', 'HomeController@index')->where('any', '.+');

    });
});
