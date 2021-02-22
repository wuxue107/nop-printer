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
    return redirect('/printer-setting');
    // return view('welcome');
});

Route::get('/printer-setting',function(){
    $localPrinters = \App\Helpers\PrinterHelper::getLocalPrinters();
    return view('printer-setting', ['localPrinters' => $localPrinters]);
});

Route::get('/tpl-manager',function(){
    return view('tpl-manager', []);
});

Route::get('/tpl-manager',function(){
    return view('tpl-manager', []);
});
Route::get('/tpl-html',function(){

    $job_key = Request::get("job_key");
    
    $params = Cache::get($job_key);
    
    $errorMsg = null;
    if(!$params){
        $params = [
            'errorMsg' => '该打印任务已失效'
        ];
    }
 
    return view('tpl-html', $params);
});
