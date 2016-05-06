<?php

include __DIR__ . '/Image.php';

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class GeraTemplate extends Image {

  private $coordNeg = false;

  public function exec($arquivo){
    $this->inicializar($arquivo);
    $this->localizarAncoras();

    $minArea = 1000;
    $maxArea = 100000;

    // Tornar atributos
    $minY = 70;
    $maxY = 180;
    $qtdLinhas = 27;
    $qtdColunas = 3;

    # busca todos os componentes conexos com área limitado por $minArea e $maxArea
    $pontos = $this->buscador->getPontosDeQuadrado($this->image, 0, 0, imagesx($this->image), imagesy($this->image));
    $objetos = $this->buscador->separaObjetos($pontos, $minArea, $maxArea);
    #Helper::pintaObjetos($this->image, $objetos);

    $centros = array_map(function($i){ return $i->getCentro(); },$objetos);
    $ancora1 = $this->ancoras[1]->getCentro();;
    // Mantem somente pontos em certa área
    $elipses = array_filter($centros,function ($i) use($ancora1,$minY,$maxY){
      $y = ($i[1]-$ancora1[1])/$this->escala;
      return $y > $minY && $y < $maxY;
    });

    // Ordena por linha-colune
    $funcSort = function($a,$b){
        if(ceil($a[1]) == ceil($b[1])){
          return $a[0] >= $b[0];
        } elseif($a[1] > $b[1]){
          return 1;
        } else {
          return -1;
        }
    };
    usort($elipses,$funcSort);

    $colunasPorLinha = 15;
    $linhas = array_chunk($elipses,$colunasPorLinha);

    $linhas = array_map(function($i){
      return array_chunk($i, 5);
    },$linhas);

    array_map(function($i){
      echo count($i) . "-";
    },$linhas);

    $output = '';
    $colunas=[];
    $copia = Helper::copia($this->image);
    for ($k = 0; $k < $qtdColunas; $k++) { # Cada coluna eg: 0 1 2
      $colunas[$k] = [];
      for($j=0;$j<$qtdLinhas;$j++){  // 0 3 6 9 e 1 4 7 10 e 2 5 8 11
        if(isset($linhas[$j][$k])){
          $colunas[$k][] = $linhas[$j][$k];
        }
      }
    }
    foreach ($colunas as $col) {
      foreach ($col as $l) {
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
          $ancBase = $this->getAncoraMaisProx($c);
          $base = $this->ancoras[$ancBase]->getCentro();


          $valorX = (($c[0]-$base[0])/$this->escala);
          $valorY = (($c[1]-$base[1])/$this->escala);

          echo $valorX . " ";
          echo $valorY . "\n";

          $output .= 'array(0,'.str_pad(number_format($valorX,11),17,'0',STR_PAD_LEFT).','.str_pad(number_format($valorY,11),17,'0',STR_PAD_LEFT).',\''.strtoupper($char).'\',\'W\','.$ancBase.'),' . "\n";

          $count++;
        }
        $output .= "\n";
      }
      $output .= "\n";
      $output .= "\n";
    }
    imagejpeg($copia,__DIR__.'/../image/asd.jpg');
    // echo '--' . count($objetos);
    imagedestroy($this->image);

    $handle = fopen(__DIR__.'/../image/teste.php','w');
    fwrite($handle,$output);
    fclose($handle);


    echo "\n--------------------------------\n";

    $a1 = $this->ancoras[1]->getCentro();
    $a2 = $this->ancoras[2]->getCentro();
    $a3 = $this->ancoras[3]->getCentro();

    echo (($a2[0] - $a1[0]) / $this->escala) . "\n";
    echo (($a3[1] - $a2[1]) / $this->escala) . "\n";

    echo "\n--------------------------------\n";
    exit;

    imagedestroy($this->image);

    exit;

  }

  private function getAncoraMaisProx($ponto){
    if($this->coordNeg){
      $a1 = $this->ancoras[1]->getCentro();
      $a2 = $this->ancoras[2]->getCentro();
      $a3 = $this->ancoras[3]->getCentro();
      $a4 = $this->ancoras[4]->getCentro();
      $ancoras = [$a1,$a2,$a3,$a4];
      $dists = array_map(function($i) use($ponto){
        return Helper::dist($ponto,$i);
      },$ancoras);
      $indice = array_keys($dists, min($dists));
      return $indice[0]+1;
    } else {
      return 1;
    }

  }


}
