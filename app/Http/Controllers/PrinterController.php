<?php
/**
 * Created by PhpStorm.
 * User: nop
 * Date: 2021-01-26
 * Time: 14:52
 */

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\PrinterHelper;
use App\Http\Controllers\Controller;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\Request;

class PrinterController extends Controller
{

    public function setting(){

        return view('printer-setting', ['name' => 'James']);
    }

    public function getLocalPrinters(){
        $printers = PrinterHelper::getLocalPrinters();

        return Helper::successMsg(["list" => $printers]);
    }

}
