<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'doctors',
],function () {
    Route::get('/', [\App\Http\Controllers\DoctorController::class, 'index']);
    Route::put('/{id}', [\App\Http\Controllers\DoctorController::class, 'update']);
    Route::put('/{id}/delay_surgery_end_time', [\App\Http\Controllers\DoctorController::class, 'delaySurgeryEndTime']);
});

Route::group([
    'prefix' => 'surgeries',
],function () {
    Route::get('/', [\App\Http\Controllers\SurgeryController::class, 'index']);
});

