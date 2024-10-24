<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SliderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', \App\Http\Controllers\Api\Auth\RegisterController::class);
Route::get('verify-email/{token}', '\App\Http\Controllers\Api\UserController@verifyemail');
Route::post('testemail', [UserController::class, 'sendTestEmail']);
Route::post('web/check/', [CodeController::class, 'check']);
Route::get('check/{code}', [CodeController::class, 'mobileCheck']);
Route::post('tesmongo', [CodeController::class, 'tesmongo']);
Route::post('login', [UserController::class, 'login']);
Route::post('claim/{code}', [ProductController::class, 'claimProduct']);
Route::get('myproduct', [ProductController::class, 'myProduct']);
Route::get('/categories', [ProductController::class, 'getCategories']);
Route::get('/categories/{categoryId}/products', [ProductController::class, 'getProductsByCategory']);
Route::post('updatep', [UserController::class, 'updateProfile']);
Route::get('wallet/point', '\App\Http\Controllers\WalletController@getWallet');
Route::resource('slider', '\App\Http\Controllers\SliderController');
