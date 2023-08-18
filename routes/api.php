<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\V1\CampaignController;
use App\Http\Controllers\V1\AutoResponseController;
use App\Http\Controllers\V1\VoipController;
use App\Http\Controllers\V1\OptOutController;
use App\Http\Controllers\V1\TwilioCallBackController;
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
        Route::get('/reports', 'index');
        Route::post('/csv', 'store');
        Route::get('/schedule/bulk-sms', 'getScheduleSms');
    });

Route::controller(AutoResponseController::class)
    ->prefix('auto-response')
    ->group(function () {
        Route::post('/store', 'store');
//        Route::post('/send', 'autoResponder');
    });


Route::group(['prefix'=>'twilio'], function(){
    Route::post('/sms-auto-responder-callback', [AutoResponseController::class, 'autoResponder']);
    Route::post('/sms-delivery-status-callback/{campaign_id}', [TwilioCallBackController::class, 'changeSMSDeliveryStatus']);
    Route::post('/voip' , [VoipController::class, 'respondToIncomingCall']);
});

Route::controller(VoipController::class)
    ->prefix('voip')
    ->group(function () {
        Route::post('/store', 'store');
    });

Route::controller(OptOutController::class)
    ->prefix('optout')
    ->group(function () {
        Route::post('/store', 'store');
    });

Route::get('/campaigns/sample/csv', function () {
    $filepath = public_path('data-samples/campaigns.csv');
    $base64 = base64_encode(file_get_contents($filepath));
    return $base64;
});

Route::get('/optouts/sample/csv', function () {
    $filepath = public_path('data-samples/optouts.csv');
    $base64 = base64_encode(file_get_contents($filepath));
    return $base64;
});
