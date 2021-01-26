<?php
namespace App\Helpers;

use http\Exception\InvalidArgumentException;
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
}
