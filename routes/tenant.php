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

use App\Http\Controllers\Tenant\AccountController;
use App\Http\Controllers\Tenant\BranchesController;
use App\Http\Controllers\Tenant\CashierController;
use App\Http\Controllers\Tenant\HomeController;
use Illuminate\Support\Facades\Route;

Route::namespace("Tenant")
     ->middleware('tenant')
     ->domain('{subdomain}.' . config('app.url_base'))
     ->group(function () {
         Route::get('/health-check', [HomeController::class, 'healthCheck']);
         Route::get('/login', [AccountController::class ,'login']);
         Route::post('/login', [AccountController::class ,'processLogin']);

         Route::middleware('tenant.auth')->group(function () {
             Route::get('', [HomeController::class, 'index']);
             Route::post('/logout', [AccountController::class, 'logout']);
             Route::get('/user-data', [HomeController::class, 'getUserData']);
             Route::get('/form-options', [HomeController::class, 'getFormOptions']);
             Route::get('/salable-products', [HomeController::class, 'getSalableProducts']);
             Route::get('/purchasable-products', [HomeController::class, 'getPurchasableProducts']);
             Route::get('/get-item-by-barcode', [HomeController::class, 'getItemByBarcode']);
             Route::get('/countries', [HomeController::class, 'getCountries']);

             Route::prefix('v1')->group(function () {
                 ################################ TENANT ADMIN ##############################################
                 Route::middleware('tenant.admin')->group(function () {
                     Route::group(['prefix' => 'units'], function () {
                         Route::get('', 'UnitsController@index');
                         Route::post('', 'UnitsController@store');
                         Route::put('', 'UnitsController@update');
                         Route::delete('', 'UnitsController@delete');
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
                         Route::get('', [BranchesController::class,'index']);
                         Route::post('', [BranchesController::class,'store']);
                         Route::put('{id}', [BranchesController::class,'update']);
                         Route::delete('{id}', [BranchesController::class,'delete']);
                         Route::patch('{id}/lock', [BranchesController::class,'lock']);
                         Route::patch('{id}/unlock', [BranchesController::class,'unlock']);
                     });

                     Route::group(['prefix' => 'users'], function () {
                         Route::get('', 'UsersController@index')->name('users');
                         Route::post('', 'UsersController@store')->name('users.create');
                         Route::put('', 'UsersController@update')->name('users.update');
                         Route::delete('', 'UsersController@delete')->name('users.delete');
                         Route::patch('lock', 'UsersController@lock')->name('users.lock');
                         Route::patch('unlock', 'UsersController@unlock')->name('users.unlock');
                     });

                     Route::group(['prefix' => 'stores'], function () {
                         Route::post('complete-purchase-transaction', 'StorePurchaseController@index')->name('pos.create_purchase');
                         Route::patch('cancel-purchase-transaction', 'CashierController@cancelPurchaseTransaction')->name('pos.cancel_purchase');
                     });
                 });
                 ################################ END TENANT ADMIN ##########################################

                 ################################ TENANT MANAGER ############################################
                 Route::middleware('tenant.manager')->group(function () {

                 });
                 ################################ END TENANT MANAGER ########################################

                 ################################ TENANT SHOP ##############################################
                 Route::middleware('tenant.cashier')->group(function () {
                     Route::get('shop-info', [CashierController::class, 'getShopInfo']);
                     Route::post('complete-sale-transaction', [CashierController::class, 'completeSaleTransaction']);
                     Route::patch('cancel-sale-transaction', [CashierController::class, 'cancelSaleTransaction']);
                 });
                 ################################ END TENANT SHOP ##########################################
             });

             Route::get('{any?}', [HomeController::class, 'index'])->where('any', '.+');
         });
     });
