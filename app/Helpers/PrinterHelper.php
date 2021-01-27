<?php
namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ProcessUtils;
use Mike42\Escpos\CapabilityProfile;

class PrinterHelper
{
    public static function getConfig(){

        $configFile = base_path('printer.json');;
        if(!is_file($configFile)){
            throw new \Exception("配置文件不存在");
        }
        $config = @json_decode(file_get_contents($configFile),true);
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new \Exception("无效的配置文件");
        }
        return $config;
    }

    public static function setConfig($config){
        $configFile = base_path('printer.json');;

        return file_put_contents($configFile,json_encode($config,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public static function getPrinter(){
        $config = self::getConfig();
        $printConnectorClass = $config['PrintConnectorClass'];
        $connector = new $printConnectorClass($config["printer"]);

        if(empty($config["CapabilityProfile"])){
            $profile = null;
        }else{
            $profile = CapabilityProfile::load($config["CapabilityProfile"]);
        }


        /* Print a "Hello world" receipt" */
        $printer = new NopPrinter($connector,$profile);

        return $printer;
    }


    public static function getFile($glob){
        $matches = File::glob($glob);
        if(empty($matches)){
            throw new \Exception("无法找到文件:{$glob}");
        }

        return $matches[0];
    }

    public static function getLocalPrinters(){
        $printersInfo = mb_convert_encoding(shell_exec('wmic printer list /format'),'UTF-8','GBK');
        $lines = explode(PHP_EOL,$printersInfo);
        $printers = [];
        $row = [];
        foreach($lines as $line){
            $line = trim($line);
            if($line === ""){
                if(!empty($row)){
                    $printers[] = $row;
                    $row = [];
                }
                continue;
            }

            $item = explode('=',trim($line),2);
            if(!isset($item[1])){
                var_dump($item);exit;
            }
            $row[$item[0]] = $item[1];
        }
        if(!empty($row)){
            $printers[] = $row;
        }

        return $printers;
    }

    public static function sharePrinter($printerName,$shareName){
        $prncnfgFile = self::getFile("C:\\Windows\\System32\\Printing_Admin_Scripts\\*\\prncnfg.vbs");
        $cmd = 'cscript ' . ProcessUtils::escapeArgument($prncnfgFile) . ' -t -p '.ProcessUtils::escapeArgument($printerName).' -h '.ProcessUtils::escapeArgument($shareName).' +shared';
        shell_exec($cmd);
    }

    public static function printerIsShared($shareName){
        $cmd = 'wmic share get Name';
        $shareNames = explode(PHP_EOL,shell_exec($cmd));

        return in_array($shareName,$shareNames);
    }

    public static function setDefaultPrinter($printerName){
        shell_exec('rundll32 printui.dll,PrintUIEntry /y /n ' . ProcessUtils::escapeArgument($printerName));
    }
}
