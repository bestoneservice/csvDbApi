<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\DatasetController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::prefix('v1')->group(function(){
	Route::post('store-file', 'FileController@store');
});*/

Route::prefix('v1')->group(function(){
	Route::post('store-records', [FileController::class, 'records']);
	Route::post('store-events', [FileController::class, 'events']);
	Route::get('get-data/{id}', [DatasetController::class, 'getRecordWithEvents']);
});