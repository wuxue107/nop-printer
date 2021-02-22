<?php

namespace App\Jobs;

use App\Helpers\Helper;
use App\Helpers\PrinterHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImagePrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $imageFile;
    public $printerName;

    /**
     * ImagePrintJob constructor.
     *
     * @param $imageFile
     * @param $printerName
     */
    public function __construct($imageFile,$printerName)
    {
        $this->imageFile = $imageFile;
        $this->printerName = $printerName;
        $this->queue = 'image-print';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Helper::writeLog([$this->printerName,$this->imageFile]);
            $printer = PrinterHelper::getPrinter($this->printerName);
            $printer->printImage($this->imageFile);
            $printer->cut();
            $printer->close();
        }catch(\Throwable $e){
            $this->fail($e);
        }
    }
}
