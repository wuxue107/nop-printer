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

Route::prefix('job')->group(function(){
    Route::post('print-image-data-url',[\App\Http\Controllers\JobController::class, 'printImageDataUrl']);
    Route::post('print-tpl',[\App\Http\Controllers\JobController::class, 'printTpl']);
    Route::post('print-html',[\App\Http\Controllers\JobController::class, 'printHtml']);
});

Route::prefix('printer')->group(function(){
    Route::get('get-local-printers',[\App\Http\Controllers\PrinterController::class, 'getLocalPrinters']);
    Route::get('get-config',[\App\Http\Controllers\PrinterController::class, 'getConfig']);
    Route::post('set-printer-config',[\App\Http\Controllers\PrinterController::class, 'setPrinterConfig']);
    Route::post('remove-printer-config',[\App\Http\Controllers\PrinterController::class, 'removePrinterConfig']);
    Route::post('set-default-printer',[\App\Http\Controllers\PrinterController::class, 'setDefaultPrinter']);
});

