<?php

class OCR {

  public function exec($image,$p1,$p2){
    $copia = imagecreatetruecolor($p2[0]-$p1[0], $p2[1]-$p1[1]);
    imagecopy($copia, $image, 0, 0, $p1[0], $p1[1], $p2[0], $p2[1]);

    $tempFile = md5(rand(0,9999).microtime(true));
    Helper::cria($copia,$tempFile);
    imagedestroy($copia);

    $path = __DIR__ . '/../data/runtime/' . $tempFile . '.png';

    $output = $this->run($path);
    if(!DEBUG){
      unlink($path);
    }

    return preg_replace('/[^0-9]/','', $output);
  }

  private function run($file){
    $handle = popen('tesseract ' . $file . ' -psm 8 stdout', 'r');
    $read = fread($handle, 2096);
    $output = $read;
    pclose($handle);
    return trim($output);
  }

}
