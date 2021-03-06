<?php

namespace App\Jobs;

use App\Helpers\NopPrinter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HtmlPrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $html;
    public $printerName;
    public $width = 0;
    public $height = 0;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($html,$printerName,$width = 0,$height = 0)
    {
        $this->html = $html;
        $this->printerName = $printerName;
        $this->width = $width;
        $this->height = $height;
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
            $data = [
                "errorMsg" => '',
                "isTpl" => false,
                "tplName" => '',
                "pageWidth" => intval($this->width),
                "pageHeight" => intval($this->height),
                "tplParams" => null,
                'printTpl' => null,
                "htmlContent" => $this->html,
            ];
            
            $url = "http://127.0.0.1:8077/tpl-html?job_key={$key}";
            echo "page url: $url";
            \Cache::set($key,$data,3600);
            //$image = NopPrinter::serverUrl2Image($url);
            $image = NopPrinter::url2Image($url);
            \Cache::delete($key);
            echo " => {$image}\n";
            if($image){
                dispatch(new ImagePrintJob($image,$this->printerName));
            }
        }catch(\Throwable $e){
            echo " => {$e->getMessage()}\n";
            $this->fail($e);
        }
    }
}
