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

    public function setDefaultPrinter(){
        $printer_name = Request::json("printer_name");
        if(empty($printer_name)){
            return Helper::failMsg("无效的参数");
        }

        $printers = PrinterHelper::getLocalPrinters();

        $printerInfos = array_column($printers,null,'Name');
        if(!isset($printerInfos[$printer_name])){
            return Helper::failMsg("找不到此打印机");
        }

        $defaultPrinter = [
            "PrinterName" => $printer_name,
            "ShareName" => "",
            "ServerName" => "",
            "PrintConnectorClass" =>"",
            "CapabilityProfile" => "",
        ];
        $printerInfo = $printerInfos[$printer_name];

        $defaultPrinter['ShareName'] = $printerInfo['ShareName']??"";
        $defaultPrinter['ServerName'] = $printerInfo['ServerName']??"";
        if(windows_os()){
            $defaultPrinter['PrintConnectorClass'] = "Mike42\\Escpos\\PrintConnectors\\WindowsPrintConnector";

            PrinterHelper::sharePrinter($printer_name,$printer_name);
            if(!PrinterHelper::printerIsShared($printer_name)){
                return Helper::failMsg("设置共享打印机：{$printer_name} 失败");
            }

            $defaultPrinter['ShareName'] = $printer_name;
        }

        if(!PrinterHelper::setConfig($defaultPrinter)){
            return Helper::failMsg("保存配置文件失败");
        }

        return Helper::successMsg();
    }

    public function getLocalPrinters(){
        $printers = PrinterHelper::getLocalPrinters();

        return Helper::successMsg(["list" => $printers]);
    }

}
