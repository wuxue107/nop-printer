<?php
/**
 * Created by PhpStorm.
 * User: nop
 * Date: 2021-01-26
 * Time: 14:52
 */

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\NopPrinter;
use App\Helpers\PrinterHelper;
use App\Http\Controllers\Controller;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\Request;

class JobController extends Controller
{

    /**
     * 打印图片
     */
    public function printImageDataUrl(){
        set_time_limit(10);
        $imageData = Request::json("image_data");
        $printerName = Request::json("printer_name");
        if(empty($imageData)){
            return Helper::failMsg("无效的参数");
        }

        $content = file_get_contents($imageData);
        if($content === false){
            return Helper::failMsg("无效的图片数据");
        }

        $header = strstr($imageData,';',true);
        $imageType = ltrim(strstr($header,'/',false),'/');
        $file = 'image/' . sha1($imageData) .'.'. $imageType;
        Helper::writeRuntimeFile($file,$content);
        $fullPath = Helper::getRuntimePath($file);

        $printer = PrinterHelper::getPrinter($printerName);
        $printer->printImage($fullPath);
        $printer->cut();
        $printer->close();

        return Helper::successMsg();
    }
    
    
    public function printTpl(){
        $tplName = Request::json("tpl_name");
        $tplParams = Request::json("tpl_params");
        
        return Helper::successMsg(NopPrinter::url2Image("http://127.0.0.1:8077/tpl-html"));
    }
}
