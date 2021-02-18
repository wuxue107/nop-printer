<?php
namespace App\Helpers;

use http\Exception\InvalidArgumentException;
use Illuminate\Support\ProcessUtils;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\GdEscposImage;
use Mike42\Escpos\PrintConnectors\PrintConnector;
use Mike42\Escpos\Printer;

class NopPrinter extends Printer
{
    public function printImage($file,$allowOptimisations = false){
        $img = GdEscposImage::load($file,$allowOptimisations);

        $this->bitImage($img);
    }
    
    static function url2Image($url,$imagePath = null){
        $phantomjsBin = base_path('archive\\phantomjs-2.1.1-windows\\bin\\phantomjs.exe');
        $scriptFile = base_path('bin\\web_capture.js');
        if(!$imagePath){
            $imagePath = Helper::getRuntimePath('image/' . sha1(microtime(true)) .'.png');
        }

        $cmd = "\"$phantomjsBin\"  --disk-cache=true  \"$scriptFile\" " .  ProcessUtils::escapeArgument($url) . " " . ProcessUtils::escapeArgument($imagePath);
        
        Helper::writeLog($cmd);
        shell_exec($cmd);
        if(file_exists($imagePath)){
            return $imagePath;
        }
        
        return false;
    }
    
    static function dataUrl2Image($imageData){
        $content = @file_get_contents($imageData);
        if($content === false){
            return false;
        }
        
        $header = strstr($imageData,';',true);
        $imageType = ltrim(strstr($header,'/',false),'/');
        $file = 'image/' . sha1($imageData) .'.'. $imageType;
        
        Helper::writeRuntimeFile($file,$content);
        $fullPath = Helper::getRuntimePath($file);
        if(!file_exists($file)){
            return false;
        }
        
        return $fullPath;
    }
    
    public function printByTpl($tplName,$params = []){
        
    }
}
