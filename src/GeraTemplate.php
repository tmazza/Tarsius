<?php

include __DIR__ . '/Image.php';

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class GeraTemplate extends Image {


  public function exec($arquivo){
    $this->inicializar($arquivo);
    $this->localizarAncoras();

    $minArea = 1000;
    $maxArea = 100000;
    # busca todos os componentes conexos com área limitado por $minArea e $maxArea
    $pontos = $this->buscador->getPontosDeQuadrado($this->image, 0, 0, imagesx($this->image), imagesy($this->image));
    $objetos = $this->buscador->separaObjetos($pontos, $minArea, $maxArea);
    #Helper::pintaObjetos($this->image, $objetos);


    $centros = array_map(function($i){ return $i->getCentro(); },$objetos);

    // Ordena por linha-colune
    usort($centros,function($a,$b){
        if(ceil($a[0]) == ceil($b[0])){
          return ceil($a[1]) >= ceil($b[1]);
        } elseif($a[0] > $b[0]){
          return 1;
        } else {
          return -1;
        }
    });

    // Tornar atributos
    $minY = 110;
    $maxY = 230;
    $qtdLinhas = 25;
    $qtdColunas = 4;

    $ancora1 = $this->ancoras[1]->getCentro();;
    // Mantem somente pontos em certa área
    $elipses = array_filter($centros,function ($i) use($ancora1,$minY,$maxY){
      $y = ($i[1]-$ancora1[1])/$this->escala;
      return $y > $minY && $y < $maxY;
    });

    // agrupa em colunas de 25 linhas
    $colunas = array_chunk($elipses,$qtdLinhas);

    $linhas = [];
    for($i=0;$i<$qtdLinhas;$i++){
      $linhas[$i] = array_column($colunas,$i);
    }

    $linhas = array_map(function($i){ return array_chunk($i,5); },$linhas);

    $colunas = [];
    for($i=0;$i<$qtdColunas;$i++){
      $colunas[$i] = array_column($linhas,$i);
    }
    $output = '';
    $copia = Helper::copia($this->image);


    foreach ($colunas as $col) { // Cada coluna
      foreach ($col as $l) { // Cada linha
        $cor = imagecolorallocate($copia,rand(0,255),0,rand(0,255));
        $count = 0;
        foreach ($l as $c) {
          $char = 'a';
          $i = $count;
          while($i>0){
            $char++;
            $i--;
          }
          imagefilledellipse($copia,$c[0],$c[1],30,30,$cor);

          $base = $this->getAncoraMaisProx($c);
          echo (($c[0]-$base[0])/$this->escala) . " ";
          echo (($c[1]-$base[1])/$this->escala) . "\n";
          $output .= 'array(0,'.(($c[0]-$base[0])/$this->escala).','.(($c[1]-$base[0])/$this->escala).',\''.strtoupper($char).'\',\'W\'),' . "\n";

          $count++;
        }
        $output .= "\n";
      }
    }
    imagejpeg($copia,__DIR__.'/../image/asd.jpg');
    // echo '--' . count($objetos);
    imagedestroy($this->image);

    $handle = fopen(__DIR__.'/../image/teste.php','w');
    fwrite($handle,$output);
    fclose($handle);

    echo '<pre>';
    print_r($output);
    exit;


    exit;

    imagedestroy($this->image);

    exit;

  }

  private function getAncoraMaisProx($ponto){
    $a1 = $this->ancoras[1]->getCentro();
    $a2 = $this->ancoras[2]->getCentro();
    $a3 = $this->ancoras[3]->getCentro();
    $a4 = $this->ancoras[4]->getCentro();
    $ancoras = [$a1,$a2,$a3,$a4];
    $dists = array_map(function($i) use($ponto){
      return Helper::dist($ponto,$i);
    },$ancoras);

    $indice = array_keys($dists, min($dists));
    
    return $ancoras[$indice[0]+1];
  }


}
