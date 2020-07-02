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

Route::group([
    'prefix' => 'v1/diagnosis',
    'namespace' => 'API'
], function () {
    Route::get('token', 'ProcessController@token')->name('token');
    Route::get('symptoms', 'ProcessController@symptoms')->name('symptoms');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

