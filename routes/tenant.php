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
use App\Http\Controllers\Tenant\BranchController;
use App\Http\Controllers\Tenant\CashierController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\ManagerController;
use App\Http\Controllers\Tenant\OrderController;
use App\Http\Controllers\Tenant\Settings\SettingsController;
use App\Http\Controllers\Tenant\UserController;
use Illuminate\Support\Facades\Route;

Route::namespace("Tenant")
     ->middleware('tenant')
     ->domain('{subdomain}.' . config('app.url_base'))
     ->group(function () {
         Route::get('', [HomeController::class, 'index']);
         Route::get('/login', [AccountController::class ,'login']);
         Route::post('/login', [AccountController::class ,'processLogin']);

         Route::group(['middleware' => ['ensure.authenticated']], function () {
             Route::get('/', [HomeController::class, 'index']);
             Route::get('/user-data', [HomeController::class, 'getUserData']);
             Route::post('/logout', 'AccountController@logout')->name('logout');
             Route::get('/form-options', [HomeController::class, 'getFormOptions']);
             Route::get('/salable-products', [HomeController::class, 'getSalableProducts']);
             Route::get('/purchasable-products', [HomeController::class, 'getPurchasableProducts']);
             Route::get('/get-item-by-barcode', [HomeController::class, 'getItemByBarcode']);
             Route::get('/countries', [HomeController::class, 'getCountries']);

             ################################ TENANT ADMIN ##############################################
             Route::prefix('admin')->middleware('ensure.admin')->group(function () {
                 Route::get('', [HomeController::class, 'admin']);

                 Route::prefix('api')->group(function () {
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

                     Route::group(['prefix' => 'stores'], function () {
                         Route::post('complete-purchase-transaction', 'StorePurchaseController@index')->name('pos.create_purchase');
                         Route::patch('cancel-purchase-transaction', 'CashierController@cancelPurchaseTransaction')->name('pos.cancel_purchase');
                     });
                 });

                 Route::get('{any?}', [HomeController::class, 'admin'])->where('any', '.+');
             });
             ################################ END TENANT ADMIN ##########################################

             ################################ TENANT MANAGER ############################################
             Route::prefix('manager')->middleware('ensure.manager')->group(function () {
                 Route::get('', [HomeController::class, 'manager']);

                 Route::prefix('api')->group(function () {

                 });

                 Route::get('{any?}', [HomeController::class, 'manager'])->where('any', '.+');
             });
             ################################ END TENANT MANAGER ########################################

             ################################ TENANT SHOP ##############################################
             Route::prefix('pos')->middleware('ensure.cashier')->group(function () {
                 Route::get('', [HomeController::class, 'pos']);

                 Route::prefix('api')->group(function () {
                     Route::get('shop-info', [CashierController::class, 'getShopInfo']);
                     Route::post('complete-sale-transaction', [CashierController::class, 'completeSaleTransaction']);
                     Route::patch('cancel-sale-transaction', [CashierController::class, 'cancelSaleTransaction']);
                 });

                 Route::get('{any?}', [HomeController::class, 'pos'])->where('any', '.+');
             });
             ################################ END TENANT SHOP ##########################################
         });
     });
