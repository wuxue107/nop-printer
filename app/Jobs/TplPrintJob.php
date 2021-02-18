<?php

namespace App\Jobs;

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
        $image = NopPrinter::url2Image("http://127.0.0.1:8077/tpl-html");
        if($image){
            dispatch(new ImagePrintJob($image,$this->printerName));
        }
    }
}
