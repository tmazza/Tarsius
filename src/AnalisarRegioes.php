<?php

include_once __DIR__.'/OCR.php';

class AnalisarRegioes {
  const TipoELipse = 0;
  const TipoOCR = 1;


  private $image;

  public $pontoBase=array(0,0);
  public $regioes=array();
  private $debug;

  private $erroX = false;
  private $erroY = false;

  public function __construct($image){
    $this->image = $image;
    if(DEBUG){
      $this->debugImage = Helper::copia($image->image);
    }
  }

  public function exec(){
    $time = microtime(true);

    $regioes = [];
    $medidas = $this->image->medidas['regioes'];

    foreach ($this->image->getRegioes() as $id => $r) {
      $tipo = $medidas[$id][0];
      if($tipo == self::TipoELipse){ # 0:ELIPSE
        $e = $this->getPontoNormalizado($r,$medidas[$id]);
        list($px,$py) = $e;
        list($taxaPreenchimento,$retorno) = $this->interpretaElipse($id,$r,$e);
        $regioes[$id] = [
          $retorno,
          $taxaPreenchimento,
          $px,
          $py,
        ];
      } elseif ($tipo == self::TipoOCR) { # [1,[x1,y1],[x2,y2]]
        $ocr = new OCR();
        $base = $this->image->ancoras[1]->getCentro();
        $p1 = Helper::rotaciona($r[1],$base,$this->image->rot);
        $p2 = Helper::rotaciona($r[2],$base,$this->image->rot);
        $p1 = [$p1[0]+$base[0],$p1[1]+$base[1]];
        $p2 = [$p2[0]+$base[0],$p2[1]+$base[1]];
        $retorno = $ocr->exec($this->image->image,$p1,$p2);

        if(DEBUG){
          $cor3 = imagecolorallocate($this->debugImage, 0, 0, 255); # DEBUG
          imagefilledrectangle($this->debugImage,$p1[0],$p1[1],$p2[0],$p2[1],$cor3);
        }

        $regioes[$id] = [
          $retorno,
          $p1,
          $p2,
        ];
      } else {
        throw new Exception('Tipo de regiÃ£o desconhecida.', 500);
      }
    }
    $this->image->output['regioes'] = $regioes;

    if(DEBUG) {
      Helper::cria($this->debugImage, 'ELIPISES_'.basename($this->image->arquivo));
      $this->image->saveTime('_analisarRegioes', $time);
    }
  }

  private function getPontoNormalizado($r,$d){
    if(isset($this->image->medidas['refAncoras'])){
      $refAncoras = $this->image->medidas['refAncoras'];
      if($refAncoras==2){
        return $this->getPontoNormalizadoDouble($r,$d);
      } elseif($refAncoras==4){
        return $this->getPontoNormalizadoQuadruple($r,$d);
      }
    }
    return $this->getPontoNormalizadoSingle($r,$d);
  }

  private function getPontoNormalizadoSingle($r,$d){
    $px = $r[1];
    $py = $r[2];

    $ancBase = isset($d[5]) ? $d[5] : 1;

    $base = $this->image->ancoras[$ancBase]->getCentro();
    if($px > 0 && $py > 0){
      $base = $this->image->ancoras[1]->getCentro();
    } elseif($px < 0 && $py > 0){
      $base = $this->image->ancoras[2]->getCentro();
    } elseif($px < 0 && $py < 0){
      $base = $this->image->ancoras[3]->getCentro();
    } elseif($px > 0 && $py < 0){
      $base = $this->image->ancoras[4]->getCentro();
    }

    $px += $base[0];
    $py += $base[1];

    # $px += bcmul($d[1],$this->getErroX() - $erroBaseX);
    # $py += bcmul($d[2],$this->getErroY() - $erroBaseY);

    $p = [$px,$py];

    return Helper::rotaciona($p,$base,$this->image->rot);

  }

  private function getPontoNormalizadoDouble($r,$d){
    if(DEBUG){
      $cor3 = imagecolorallocate($this->debugImage, 0, 0, 255); # DEBUG
      $cor4 = imagecolorallocate($this->debugImage, 255, 255, 0); # DEBUG
      $cor5 = imagecolorallocate($this->debugImage, 0, 0, 0); # DEBUG
    }

    $p1 = $r[1];
    $p3 = $r[2];

    $ancora1 = $this->image->ancoras[1]->getCentro();
    $ancora3 = $this->image->ancoras[3]->getCentro();

    $p1 = [bcadd($p1[0],$ancora1[0]),bcadd($p1[1],$ancora1[1])];
    $p3 = [bcadd($p3[0],$ancora3[0]),bcadd($p3[1],$ancora3[1])];

    if(DEBUG){
      imagefilledellipse($this->debugImage, $ancora1[0], $ancora1[1], 3, 3, $cor3); # DEBUG
      imagefilledellipse($this->debugImage, $ancora3[0], $ancora3[1], 3, 3, $cor4); # DEBUG
    }


    $p1 = Helper::rotaciona($p1,$ancora1,$this->image->rot);
    $p3 = Helper::rotaciona($p3,$ancora3,$this->image->rot);

    if(DEBUG){
      imagefilledellipse($this->debugImage, $p1[0], $p1[1], 3, 3, $cor3); # DEBUG
      imagefilledellipse($this->debugImage, $p3[0], $p3[1], 3, 3, $cor4); # DEBUG
    }
    $minX = min($p1[0],$p3[0]); $maxX = max($p1[0],$p3[0]);
    $minY = min($p1[1],$p3[1]); $maxY = max($p1[1],$p3[1]);

    $px1 = (($maxX - $minX) / 2) + $minX;
    $py1 = (($maxY - $minY) / 2) + $minY;

    if(DEBUG){
      imagefilledellipse($this->debugImage, $px1, $py1, 3, 3, $cor5); # DEBUG
    }
    return [$px1,$py1];
  }

  private function getPontoNormalizadoQuadruple($r,$d){
    if(DEBUG){
      $cor = imagecolorallocate($this->debugImage, 255, 0, 0); # DEBUG
      $cor2 = imagecolorallocate($this->debugImage, 0, 255, 0); # DEBUG
      $cor3 = imagecolorallocate($this->debugImage, 0, 0, 255); # DEBUG
      $cor4 = imagecolorallocate($this->debugImage, 255, 255, 0); # DEBUG
      $cor5 = imagecolorallocate($this->debugImage, 0, 0, 0); # DEBUG
      $cor6 = imagecolorallocate($this->debugImage, 255, 0, 255); # DEBUG
    }

    list($p1,$p3) = $r[1];
    list($p2,$p4) = $r[2];

    $ancora1 = $this->image->ancoras[1]->getCentro();
    $ancora3 = $this->image->ancoras[3]->getCentro();
    $ancora2 = $this->image->ancoras[2]->getCentro();
    $ancora4 = $this->image->ancoras[4]->getCentro();

    $p1 = [bcadd($p1[0],$ancora1[0]),bcadd($p1[1],$ancora1[1])];
    $p3 = [bcadd($p3[0],$ancora3[0]),bcadd($p3[1],$ancora3[1])];
    $p2 = [bcadd($p2[0],$ancora2[0]),bcadd($p2[1],$ancora2[1])];
    $p4 = [bcadd($p4[0],$ancora4[0]),bcadd($p4[1],$ancora4[1])];

    if(DEBUG){
      imagefilledellipse($this->debugImage, $ancora1[0], $ancora1[1], 3, 3, $cor3); # DEBUG
      imagefilledellipse($this->debugImage, $ancora3[0], $ancora3[1], 3, 3, $cor4); # DEBUG
      imagefilledellipse($this->debugImage, $ancora2[0], $ancora2[1], 3, 3, $cor); # DEBUG
      imagefilledellipse($this->debugImage, $ancora4[0], $ancora4[1], 3, 3, $cor2); # DEBUG
    }

    $p1 = Helper::rotaciona($p1,$ancora1,$this->image->rot);
    $p3 = Helper::rotaciona($p3,$ancora3,$this->image->rot);
    $p2 = Helper::rotaciona($p2,$ancora2,$this->image->rot);
    $p4 = Helper::rotaciona($p4,$ancora4,$this->image->rot);

    // imagefilledellipse($this->debugImage, $p2[0], $p2[1], 3, 3, $cor); # DEBUG
    // imagefilledellipse($this->debugImage, $p4[0], $p4[1], 3, 3, $cor2); # DEBUG
    // imagefilledellipse($this->debugImage, $p1[0], $p1[1], 3, 3, $cor3); # DEBUG
    // imagefilledellipse($this->debugImage, $p3[0], $p3[1], 3, 3, $cor4); # DEBUG

    $minX = min($p1[0],$p3[0]); $maxX = max($p1[0],$p3[0]);
    $minY = min($p1[1],$p3[1]); $maxY = max($p1[1],$p3[1]);
    $px1 = (($maxX - $minX) / 2) + $minX;
    $py1 = (($maxY - $minY) / 2) + $minY;
    if(DEBUG){
      imagefilledellipse($this->debugImage, $px1, $py1, 3, 3, $cor5); # DEBUG
    }

    $minX = min($p2[0],$p4[0]); $maxX = max($p2[0],$p4[0]);
    $minY = min($p2[1],$p4[1]); $maxY = max($p2[1],$p4[1]);
    $px2 = (($maxX - $minX) / 2) + $minX;
    $py2 = (($maxY - $minY) / 2) + $minY;
    if(DEBUG){
      imagefilledellipse($this->debugImage, $px2, $py2, 3, 3, $cor6); # DEBUG
    }

    $pf1 = [$px1,$py1];
    $pf2 = [$px2,$py2];
    $minX = min($pf1[0],$pf2[0]); $maxX = max($pf1[0],$pf2[0]);
    $minY = min($pf1[1],$pf2[1]); $maxY = max($pf1[1],$pf2[1]);
    $px = (($maxX - $minX) / 2) + $minX;
    $py = (($maxY - $minY) / 2) + $minY;
    if(DEBUG){
      imagefilledellipse($this->debugImage, $px, $py, 3, 3, $cor); # DEBUG
    }

    return [$px,$py];
  }

  private function interpretaElipse($id,$r,$e){
    $taxaPreenchimento = $this->getTaxaPreenchimento($e);
    $minMatch = $this->image->preenchimentoMinimo;

    if(DEBUG){
      $cor = imagecolorallocate($this->debugImage, 255, 0, 0);
      if($taxaPreenchimento >= $minMatch){
        imagefilledellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], $cor);
      } else {
        imageellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], $cor);
      }
    }

    return [
      $taxaPreenchimento,
      ($taxaPreenchimento >= $minMatch) ? $r[3] : $r[4],
    ];

}

  // private function getErroX(){
  //   if(!$this->erroX){
  //     $a1 = $this->image->ancoras[1]->getCentro();
  //     $a2 = $this->image->ancoras[2]->getCentro();
  //     // Correção de erro de escala em X
  //     $avaliado = bcsub($a2[0],$a1[0]);
  //     $esperado = bcmul($this->image->medidas['distAncHor'],$this->image->escala);
  //     $this->erroX = bcdiv(bcsub($avaliado,$esperado),$this->image->medidas['distAncHor']);
  //   }
  //   return $this->erroX;
  // }

  // private function getErroY(){
  //   if(!$this->erroY){
  //     $a1 = $this->image->ancoras[1]->getCentro();
  //     $a4 = $this->image->ancoras[4]->getCentro();
  //     // Correção de erro de escala em Y
  //     $avaliado = bcsub($a4[1],$a1[1]);
  //     $esperado = bcmul($this->image->medidas['distAncVer'],$this->image->escala);
  //     $this->erroY = bcdiv(bcsub($avaliado,$esperado),$this->image->medidas['distAncVer']);
  //   }
  //   return $this->erroY;
  // }



  private function getTaxaPreenchimento($centro){

    $image = $this->image->image;
    $elpB = $this->image->distancias['elpAltura'];
    $elpA = $this->image->distancias['elpLargura'];

    list($x0, $y0, $x1, $y1) = $this->image->buscador->getPontosQuadradoDeBusca($elpB, $centro[0], $centro[1]);
    #Helper::rect($image, $x0, $y0, $x1, $y1, microtime(true) . '_REGIAO_DE_BUSCA__' . $elpB);
    $pontos = $this->image->buscador->getPontosDeQuadrado($image, $x0, $y0, $x1, $y1);
    #Helper::pintaPontos($image, $pontos, 'PONTOS__' . $elpB . '__' . microtime(true), [255, 255, 0]);

    # verifica se um ponto esta dentro ou fora da elipse
    $elipse = function ($x,$y) use($centro,$elpA,$elpB) {
      return (($x - $centro[0])**2 / ($elpA/2)**2) + (($centro[1]-$y)**2 / ($elpB/2)**2) <= 1;
    };
    // $np = $sp = [];
    $areaAnalisada = 0;
    foreach ($pontos as $x => $colunas) {
      foreach ($colunas as $y => $v) {
        if($elipse($x,$y))
          $areaAnalisada++;
        //   $sp[$x][$y] = 1; // Salvas pontos dentro e fora da elipse
        // } else {           // Usado para gerar imagem debug
        //   $np[$x][$y] = 1;
        // }
      }
    }
    #Helper::pintaPontos($image, $sp, 'PONTOS___' . microtime(true), [255, 0, 0]);

    # TODO: pre-calcular área
    $area = pi() * ($elpA/2) * ($elpB/2);
    // echo ceil($area) . ' - ' . $areaAnalisada . ' | ' . round($areaAnalisada / $area,2) .  "\n";

    return ($areaAnalisada / $area);
  }

}
