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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('print')->group(function(){
    Route::post('print-image-data-url',[\App\Http\Controllers\PrintController::class, 'printImageDataUrl']);
    Route::get('get-local-printers',[\App\Http\Controllers\PrintController::class, 'getLocalPrinters']);
});

