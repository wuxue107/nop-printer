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

class JobController extends Controller
{

    /**
     * 打印图片
     */
    public function printImageDataUrl(){
        $imageData = Request::json("image_data");
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

        $printer = PrinterHelper::getPrinter();
        $printer->printImage($fullPath);
        $printer->cut();
        $printer->close();

        return Helper::successMsg();
    }
}
