<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\V1\CampaignController;
use App\Http\Controllers\V1\AutoResponseController;

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

Route::get('/example', function () {
    return ['message' => 'This is an example API route.'];
});

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::get('/logout', 'logout')->middleware('auth:sanctum');
    });

Route::controller(CampaignController::class)
    ->prefix('campaign')
    ->group(function () {
        Route::post('/csv', 'store');
        Route::get('/schedule/bulk-sms', 'getScheduleSms');
    });

Route::controller(AutoResponseController::class)
    ->prefix('auto-response')
    ->group(function () {
        Route::post('/store', 'store');
    });
