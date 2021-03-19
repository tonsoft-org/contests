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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware(\App\Http\Middleware\IsAdmin::class)->group(function () {
    Route::post('contests', [\App\Http\Controllers\ContestController::class, 'store']);
});

Route::get('contests/count', [\App\Http\Controllers\ContestController::class, 'count']);
Route::get('contests', [\App\Http\Controllers\ContestController::class, 'index']);
