<?php

class OCR {

  public $image;

  public function __construct($image){
    $this->image = $image;
  }

  public function exec($regiao){
    $dists = $this->image->distancias[$regiao];
    $dists[0] += $this->image->ancoras[1]->getCentro()[0];
    $dists[1] += $this->image->ancoras[1]->getCentro()[1];
    $dists[2] += $this->image->ancoras[1]->getCentro()[0];
    $dists[3] += $this->image->ancoras[1]->getCentro()[1];

    $copia = imagecreatetruecolor($dists[2]-$dists[0], $dists[3]-$dists[1]);
    imagecopy($copia, $this->image->image, 0, 0, $dists[0], $dists[1], $dists[2], $dists[3]);

    $tempFile = 'temp/' . md5($regiao.$this->image->arquivo);
    Helper::cria($copia,$tempFile);
    imagedestroy($copia);

    $output = $this->run(__DIR__ . '/../image/' . $tempFile.'.png');
    if(!DEBUG
    ){
      unlink(__DIR__ . '/../image/' . $tempFile.'.png');
    }
    return $output;
  }

  private function run($file){
    $handle = popen('tesseract ' . $file . ' -psm 8 stdout nobatch digits', 'r');
    $read = fread($handle, 2096);
    $output = $read;
    pclose($handle);
    return trim($output);
  }

}
