<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
//    dd(date('Y-m-d H:i:s'));
    return view('welcome');
});

Route::get('/campaigns/sample/csv', function () {
    $filepath = public_path('data-samples/campaigns.csv');
    return response()->download($filepath);
});
