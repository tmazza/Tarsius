<?php

class Barcode {
  public $image;

  public function __construct($image){
    $this->image = $image;
  }

  public function exec(){
    $output = array();
    exec('python ' . __DIR__ . '/barcode.py ' . $this->image->arquivo,$output);
    #exec('C:\Python27\python.exe ' . __DIR__ . '/barcode.py ' . $this->image->arquivo,$output);

    // $handle = popen('python ' . __DIR__ . '/barcode.py ' . $this->image->arquivo, 'r');
    // $read = fread($handle, 2096);
    // $output = $read;
    // pclose($handle);
    if(count($output) > 0){
      return trim($output[0]);
    } else {
      return '';
    }
  }
}
