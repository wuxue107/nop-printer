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

    public function getConfig(){
        $config = PrinterHelper::configLoad();

        return Helper::successMsg($config);
    }

    public function setPrinterConfig(){
        set_time_limit(60);
        $printer_name = Request::json("printer_name");
        $is_default = Request::json("is_default");
        if(empty($printer_name)){
            return Helper::failMsg("无效的参数");
        }

        $printers = PrinterHelper::getLocalPrinters();

        $printerInfos = array_column($printers,null,'Name');
        if(!isset($printerInfos[$printer_name])){
            return Helper::failMsg("找不到此打印机");
        }
        $printerInfo = $printerInfos[$printer_name];
        $printerConfig = [
            "Name" => $printer_name,
            "PortName" => $printerInfo['PortName']?:"",
            "ShareName" => "",
            "ServerName" => "",
            "PrintConnectorClass" =>"",
            "CapabilityProfile" => "",
        ];

        $printerConfig['ShareName'] = $printerInfo['ShareName']?:$printer_name;
        $printerConfig['ServerName'] = $printerInfo['ServerName']??"";
        if(windows_os()){
            $printerConfig['PrintConnectorClass'] = "Mike42\\Escpos\\PrintConnectors\\WindowsPrintConnector";

            if(empty($printerConfig['ServerName'])){
                if(!PrinterHelper::printerIsShared($printer_name)){
                    PrinterHelper::sharePrinter('',$printerConfig['Name'],$printerConfig['PortName'],$printerConfig['ShareName']);
                    if(!PrinterHelper::printerIsShared($printer_name)){
                        return Helper::failMsg("打印机：{$printer_name} 无法设置为共享");
                    }
                }
            }
        }

        if(!PrinterHelper::configSetPrinter($printerConfig,$is_default)){
            return Helper::failMsg("保存配置文件失败");
        }

        return Helper::successMsg();
    }

    public function removePrinterConfig(){
        $printer_name = Request::json("printer_name");

        if(!PrinterHelper::configRemovePrinter($printer_name)){
            return Helper::failMsg();
        }

        return Helper::successMsg();
    }
    public function getLocalPrinters(){
        $printers = PrinterHelper::getLocalPrinters();

        return Helper::successMsg(["list" => $printers]);
    }

}
