<?php
namespace app\Helpers;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\GdEscposImage;


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

    public static function getPrinter(){
        $config = self::getConfig();
        $printConnectorClass = $config['PrintConnectorClass'];
        $connector = new $printConnectorClass($config["printer"]);
        $profile = CapabilityProfile::load($config["CapabilityProfile"]);

        /* Print a "Hello world" receipt" */
        $printer = new NopPrinter($connector,$profile);

        return $printer;
    }

    public static function searchPrinter(){

    }
}
