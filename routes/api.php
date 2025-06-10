<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->group(function () {
Route::prefix('/order')->controller(ApiController::class)->group(function () {
    Route::get('/{id}/{secret}', 'getOrder')->name('get.order');
});
Route::prefix('/vehicle')->controller(ApiController::class)->group(function () {
    Route::get('/{vcode}/{secret}', 'getVehicle')->name('get.vehicle');
});
Route::prefix('/orders')->controller(ApiController::class)->group(function () {
    Route::get('/{secret}', 'getOrders')->name('get.orders');
});
Route::prefix('/laborder')->controller(ApiController::class)->group(function () {
    Route::post('/{storeorderid}/{labordernumber}/{secret}', 'updateLabOrder')->name('update.lab.order');
});
//});
