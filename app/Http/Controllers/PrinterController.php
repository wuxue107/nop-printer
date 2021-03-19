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
    /**
     * 获取打印机配置信息
     * 
     * @return array
     */
    public function getConfig(){
        $config = PrinterHelper::configLoad();

        return Helper::successMsg($config);
    }

    /**
     * 配置添加打印机
     *
     * @return array
     */
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

    /**
     * 设置已添加的打印机为默认打印机
     * 
     * @return array
     */
    public function setDefaultPrinter(){
        $printer_name = Request::json("printer_name");
        PrinterHelper::setDefaultPrinter($printer_name);
        return Helper::successMsg();
    }

    /**
     * 删除已添加的打印机配置
     * 
     * @return array
     */
    public function removePrinterConfig(){
        $printer_name = Request::json("printer_name");

        if(!PrinterHelper::configRemovePrinter($printer_name)){
            return Helper::failMsg();
        }

        return Helper::successMsg();
    }

    /**
     * 获取本地打印机
     * 
     * @return array
     */
    public function getLocalPrinters(){
        $printers = PrinterHelper::getLocalPrinters();

        return Helper::successMsg(["list" => $printers]);
    }

    public function clearCache(){
        //chdir(base_path());
        //shell_exec("cmd /c clear_cache.bat");
        
        return Helper::successMsg();
    }
}
