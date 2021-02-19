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
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($html,$printerName)
    {
        $this->html = $html;
        $this->printerName = $printerName;
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
            $image = NopPrinter::url2Image("http://127.0.0.1:8077/tpl-html");
            if($image){
                dispatch(new ImagePrintJob($image,$this->printerName));
            }
        }catch(\Throwable $e){
            $this->fail($e);
        }
    }
}
