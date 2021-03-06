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
use App\Jobs\HtmlPrintJob;
use App\Jobs\ImagePrintJob;
use App\Jobs\TplPrintJob;
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

        $imageFile = NopPrinter::dataUrl2Image($imageData);
        if(!$imageFile){
            return Helper::failMsg("无效的图片");
        }

        $job = new ImagePrintJob($imageFile,$printerName);
        $taskId = $this->dispatch($job);
        return Helper::successMsg(['task_id' => $taskId]);
    }

    /**
     * 打印模板
     * 
     * @return array
     */
    public function printTpl(){
        $printerName = Request::json("printer_name");
        $tplName = Request::json("tpl_name");
        $tplParams = Request::json("tpl_params");
        $job = new TplPrintJob($tplName,$tplParams,$printerName);
        
        
        $taskId = $this->dispatch($job);
        
        return Helper::successMsg(['task_id' => $taskId]);
    }

    /**
     * 打印HTML
     * 
     * @return array
     */
    public function printHtml(){
        $printerName = Request::json("printer_name");
        $html = Request::json("html");
        $job = new HtmlPrintJob($html,$printerName);


        $taskId = $this->dispatch($job);

        return Helper::successMsg(['task_id' => $taskId]);
    }
}
