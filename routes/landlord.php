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

use App\Http\Controllers\Landlord\AccountController;
use App\Http\Controllers\Landlord\TenantController;
use Illuminate\Support\Facades\Route;


Route::namespace("Landlord")
     ->domain('admin.' . config('app.url_base'))
     ->group(function () {
         Route::get('', [AccountController::class, 'index']);
         Route::get('/login', [AccountController::class, 'index']);
         Route::post('/login', [AccountController::class, 'processLogin']);

         Route::middleware('landlord')->group(function () {
             Route::get('/', [AccountController::class, 'index']);

             Route::prefix('api')->group(function () {

                 Route::get('user_data', [AccountController::class, 'getUser']);

                 Route::prefix('tenants')->group(function () {
                     Route::get('', [TenantController::class, 'index']);
                     Route::post('', [TenantController::class, 'store']);
                     Route::put('', [TenantController::class, 'update']);
                     Route::patch('block/{id}', [TenantController::class, 'block']);
                     Route::patch('unblock/{id}', [TenantController::class, 'unblock']);
                 });
             });

             Route::get('{any?}', [AccountController::class, 'index'])->where('any', '.+');
         });
     });
