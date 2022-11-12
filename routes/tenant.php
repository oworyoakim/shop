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
use App\Http\Controllers\Tenant\CategoriesController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\ItemsController;
use App\Http\Controllers\Tenant\PurchasePaymentsController;
use App\Http\Controllers\Tenant\PurchasesController;
use App\Http\Controllers\Tenant\StocksController;
use App\Http\Controllers\Tenant\StorePurchaseController;
use App\Http\Controllers\Tenant\StoreSaleController;
use Illuminate\Support\Facades\Route;

Route::namespace("Tenant")
     ->middleware('tenant')
     ->domain('{subdomain}.' . config('app.url_base'))
     ->group(function () {
         Route::get('/health-check', [HomeController::class, 'healthCheck']);
         Route::get('/login', [AccountController::class, 'login'])->name('login');
         Route::post('/login', [AccountController::class, 'processLogin']);

         Route::middleware(['auth:tenant', 'tenant.unblocked'])->group(function () {
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
                 Route::middleware(['tenant.admin'])->group(function () {
                     Route::prefix('units')->group(function () {
                         Route::get('', 'UnitsController@index');
                         Route::post('', 'UnitsController@store');
                         Route::put('', 'UnitsController@update');
                         Route::delete('', 'UnitsController@delete');
                     });

                     Route::prefix('suppliers')->group(function () {
                         Route::get('', 'SuppliersController@index')->name('suppliers');
                         Route::post('', 'SuppliersController@store')->name('suppliers.create');
                         Route::put('', 'SuppliersController@update')->name('suppliers.update');
                         Route::delete('', 'SuppliersController@delete')->name('suppliers.delete');
                     });

                     Route::prefix('categories')->group(function () {
                         Route::get('', [CategoriesController::class, 'index']);
                         Route::post('', [CategoriesController::class, 'store']);
                         Route::put('{id}', [CategoriesController::class, 'update']);
                         Route::delete('{id}', [CategoriesController::class, 'delete']);
                     });

                     Route::prefix('items')->group(function () {
                         Route::get('', [ItemsController::class, 'index']);
                         Route::post('', [ItemsController::class, 'store']);
                         Route::put('{id}', [ItemsController::class, 'update']);
                         Route::delete('{id}', [ItemsController::class, 'delete']);
                         Route::get('print-barcode/{barcode}', [ItemsController::class, 'printBarcode']);
                     });

                     Route::prefix('purchases')->group(callback: function () {
                         Route::get('', [PurchasesController::class, 'index']);
                         Route::post('', [StorePurchaseController::class, 'store']);

                         Route::put('{purchase}/pay', [PurchasePaymentsController::class, 'store']);
                     });

                     Route::prefix('stocks')->group(function (){
                         Route::get('', [StocksController::class, 'index']);
                     });

                     Route::prefix('branches')->group(function () {
                         Route::get('', [BranchesController::class, 'index']);
                         Route::post('', [BranchesController::class, 'store']);
                         Route::put('{id}', [BranchesController::class, 'update']);
                         Route::delete('{id}', [BranchesController::class, 'delete']);
                         Route::patch('{id}/lock', [BranchesController::class, 'lock']);
                         Route::patch('{id}/unlock', [BranchesController::class, 'unlock']);
                     });

                     Route::prefix('users')->group(function () {
                         Route::get('', 'UsersController@index')->name('users');
                         Route::post('', 'UsersController@store')->name('users.create');
                         Route::put('', 'UsersController@update')->name('users.update');
                         Route::delete('', 'UsersController@delete')->name('users.delete');
                         Route::patch('lock', 'UsersController@lock')->name('users.lock');
                         Route::patch('unlock', 'UsersController@unlock')->name('users.unlock');
                     });

                     Route::prefix('stores')->group(function () {
                         Route::post('complete-purchase-transaction', 'StorePurchaseController@index')->name('pos.create_purchase');
                         Route::patch('cancel-purchase-transaction', 'CashierController@cancelPurchaseTransaction')->name('pos.cancel_purchase');
                     });
                 });
                 ################################ END TENANT ADMIN ##########################################

                 ################################ TENANT MANAGER ############################################
                 Route::middleware(['tenant.manager'])->group(function () {

                 });
                 ################################ END TENANT MANAGER ########################################

                 ################################ TENANT SHOP ##############################################
                 Route::middleware(['tenant.cashier'])->group(function () {
                     Route::get('shop-info', [CashierController::class, 'getShopInfo']);
                     Route::post('sales', [StoreSaleController::class, 'store']);
                     Route::patch('sales/cancel', [CashierController::class, 'cancelSaleTransaction']);
                 });
                 ################################ END TENANT SHOP ##########################################
             });

             Route::get('{any?}', [HomeController::class, 'index'])->where('any', '.+');
         });
     });
