<?php

namespace App\Jobs;

use App\Helpers\Helper;
use App\Helpers\NopPrinter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TplPrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tplName;
    public $printerName;
    public $tplParams;

    /**
     * TplPrintJob constructor.
     *
     * @param       $tplName
     * @param array $tplParams
     * @param null  $printerName
     */
    public function __construct($tplName,$tplParams = [],$printerName = null)
    {
        $this->tplName = $tplName;
        $this->printerName = $printerName;
        $this->tplParams = $tplParams;
        $this->queue = 'tpl-print';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $key = 'tpljob-'. uniqid(true);
            $printTpl = \App\Models\PrintTpl::firstWhere('tpl_name',$this->tplName);
            $errorMsg = null;
            if(!$printTpl){
                $errorMsg = "模板不存在：" . $this->tplName;
            }


            $pageWidht = $printTpl?$printTpl->width:0;
            $pageHeight = $printTpl?$printTpl->height:0;
            $data = [
                "errorMsg" => $errorMsg,
                "isTpl" => true,
                "pageWidth" => intval($pageWidht),
                "pageHeight" => intval($pageHeight),
                "tplName" => $this->tplName,
                "tplParams" => $this->tplParams,
                'printTpl' => $printTpl?$printTpl->toArray():null,
                "htmlContent" => "",
            ];
            \Cache::set($key,$data,3600);

            $image = NopPrinter::url2Image("http://127.0.0.1:8077/tpl-html?job_key={$key}",$pageWidht,$pageHeight);
            if($image){
                dispatch(new ImagePrintJob($image,$this->printerName));
            }
        }catch(\Throwable $e){
            $this->fail($e);
        }
    }
}
