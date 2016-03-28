<?php

class OCRTeste {

  public $image;

  public function __construct($image){
    $this->image = $image;
  }

  public function exec($regiao){
    # Separa objetos da imagem
    $dists = $this->image->distancias[$regiao];
    $dists[0] += $this->image->ancoras[1]->getCentro()[0];
    $dists[1] += $this->image->ancoras[1]->getCentro()[1];
    $dists[2] += $this->image->ancoras[1]->getCentro()[0];
    $dists[3] += $this->image->ancoras[1]->getCentro()[1];
    $pontos = $this->image->buscador->getPontosDeQuadrado($this->image->image, $dists[0], $dists[1], $dists[2], $dists[3]);
    $objetos = $this->image->buscador->separaObjetos($pontos, 50, 600);

    # interpreta assinatura de cada um dos numeros
    $numeros = [];
    $possiveis = [1,3,4,6,7,8];
    foreach ($possiveis as $i) {
        $image = Helper::load(__DIR__.'/../image/ancoras/numeros/' . $i . '.jpg');
        $numeros[$i] = $this->image->getAssinatura($image,0,2000);
    }

    # para cada objeto busca o melhor match
    $count = 0;
    foreach ($objetos as $o) {
      $assObj = Assinatura::get($o);
      foreach ($numeros as $n => $assNum) {
        echo $count . ' - ' . $n . ' - ' . (Assinatura::comparaFormas($assNum,$assObj)) . '<br>';
      }
      $count++;
    }
    #7788634711
    exit;


    $copia = imagecreatetruecolor($dists[2]-$dists[0], $dists[3]-$dists[1]);
    imagecopy($copia, $this->image->image, 0, 0, $dists[0], $dists[1], $dists[2], $dists[3]);

    $tempFile = 'temp/' . md5($regiao.$this->image->arquivo);
    Helper::cria($copia,$tempFile);
    imagedestroy($copia);
    return $this->run(__DIR__ . '/../image/' . $tempFile.'.png');
    // echo '<br><br>--' . ($output != '7788634711' ? 'OPS!' . $output : 'ok' ) . '--<br><br>';
  }

  private function run($file){
    $handle = popen('tesseract ' . $file . ' -psm 8 stdout nobatch digits', 'r');
    $read = fread($handle, 2096);
    $output = $read;
    pclose($handle);
    return trim($output);
  }

}
