<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ProcessUtils;
use Mike42\Escpos\CapabilityProfile;

class PrinterHelper
{
    public static function loadConfig()
    {
        $configFile = base_path('printer.json');;
        if(!is_file($configFile)) {
            return [];
        }
        $config = @json_decode(file_get_contents($configFile), true);
        if(json_last_error() !== JSON_ERROR_NONE || !is_array($config)) {
            return [];
        }

        return $config;
    }

    public static function storeConfig($config)
    {
        $configFile = base_path('printer.json');;

        return file_put_contents($configFile, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    public static function getPrinterConfig($printerName = null)
    {
        $config = self::loadConfig();
        if(is_null($printerName)) {
            if(is_null($config['default'])) {
                throw new \Exception("未配置默认的打印机");
            }

            $printerName = $config['default'];
        }

        $printerConfig = $config[$printerName] ?? null;
        if(is_null($printerConfig)) {
            throw new \Exception("打印机:{$printerName} 缺失配置信息");
        }

        return $printerConfig;
    }

    public static function setPrinterConfig($printerConfig, $isDefault = false)
    {
        $printerName = $printerConfig['Name'];
        $config = self::loadConfig();
        if(empty($config['default']) || $isDefault){
            $config['default'] = $printerName;
        }

        if(!is_array($config['printers'])){
            $config['printers'] = [];
        }

        $config['printers'][$printerName] = $printerConfig;

        return self::storeConfig($config);
    }

    public static function getPrinter()
    {
        $printerConfig = self::getPrinterConfig();

        $printConnectorClass = $printerConfig['PrintConnectorClass'];
        $connector = new $printConnectorClass($printerConfig["PrinterName"]);
        if(empty($printerConfig["CapabilityProfile"])) {
            $profile = null;
        }
        else {
            $profile = CapabilityProfile::load($printerConfig["CapabilityProfile"]);
        }

        /* Print a "Hello world" receipt" */
        $printer = new NopPrinter($connector, $profile);

        return $printer;
    }

    public static function getFile($glob)
    {
        $matches = File::glob($glob);
        if(empty($matches)) {
            throw new \Exception("无法找到文件:{$glob}");
        }

        return $matches[0];
    }

    public static function getLocalPrinters()
    {
        $printersInfo = mb_convert_encoding(shell_exec('wmic printer list /format'), 'UTF-8', 'GBK');
        $lines = explode(PHP_EOL, $printersInfo);
        $printers = [];
        $row = [];
        foreach($lines as $line) {
            $line = trim($line);
            if($line === "") {
                if(!empty($row)) {
                    $printers[] = $row;
                    $row = [];
                }
                continue;
            }

            $item = explode('=', trim($line), 2);
            if(!isset($item[1])) {
                var_dump($item);
                exit;
            }
            $row[$item[0]] = $item[1];
        }
        if(!empty($row)) {
            $printers[] = $row;
        }

        return $printers;
    }

    public static function sharePrinter($printerName, $shareName)
    {
        $prncnfgFile = self::getFile("C:\\Windows\\System32\\Printing_Admin_Scripts\\*\\prncnfg.vbs");
        $cmd = 'cscript ' . ProcessUtils::escapeArgument($prncnfgFile) . ' -t -p ' . ProcessUtils::escapeArgument($printerName) . ' -h ' . ProcessUtils::escapeArgument($shareName) . ' +shared';
        shell_exec($cmd);
    }

    public static function printerIsShared($shareName)
    {
        $cmd = 'wmic share get Name';
        $shareNames = array_map('trim', explode(PHP_EOL, shell_exec($cmd)));

        return in_array($shareName, $shareNames);
    }

    public static function setDefaultPrinter($printerName)
    {
        shell_exec('rundll32 printui.dll,PrintUIEntry /y /n ' . ProcessUtils::escapeArgument($printerName));
    }
}
