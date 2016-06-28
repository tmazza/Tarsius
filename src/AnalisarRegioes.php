<?php

class AnalisarRegioes {
  const TipoELipse = 0;


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
      $e = $this->getPontoNormalizado($r,$medidas[$id]);
      list($px,$py) = $e;

      $tipo = $r[0];
      if($tipo == self::TipoELipse){ # 0:ELIPSE
        list($taxaPreenchimento,$retorno) = $this->interpretaElipse($id,$r,$e);
        $regioes[$id] = [
          $retorno,
          $taxaPreenchimento,
          $px,
          $py,
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

    // TODO: de acordo com o template ou fixo para cada resolução?
    // Tem como calcular esse valou ou depende da quantidade real de pixel utilizadas
    $erroBaseX = -0.0035; $erroBaseX = 0; # <-- Note que o erro base não está sendo utilizado
    $erroBaseY = -0.0081; $erroBaseY = 0; # <-- Note que o erro base não está sendo utilizado

    $px += bcmul($d[1],$this->getErroX() - $erroBaseX);
    $py += bcmul($d[2],$this->getErroY() - $erroBaseY);

    $p = [$px,$py];

    return Helper::rotaciona($p,$base,$this->image->rot);

  }

  private function interpretaElipse($id,$r,$e){
  $taxaPreenchimento = $this->getTaxaPreenchimento($e);
  $minMatch = PREENCHIMENTO_MINIMO;

  if(DEBUG){

    $closest = $this->getAncoraMaisProx($e);

    $cor = imagecolorallocate($this->debugImage, 255, 0, 0);
    // switch ($closest) {
    //   case 1: $cor = imagecolorallocate($this->debugImage, 255, 0, 0); break;
    //   case 2: $cor = imagecolorallocate($this->debugImage, 0, 255, 0); break;
    //   case 3: $cor = imagecolorallocate($this->debugImage, 0, 255, 255); break;
    //   case 4: $cor = imagecolorallocate($this->debugImage, 255, 255, 0); break;
    // }

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



// TODO: só para teste!!! ?
private function getAncoraMaisProx($ponto){
  $a1 = $this->image->ancoras[1]->getCentro();
  $a2 = $this->image->ancoras[2]->getCentro();
  $a3 = $this->image->ancoras[3]->getCentro();
  $a4 = $this->image->ancoras[4]->getCentro();
  $ancoras = [$a1,$a2,$a3,$a4];
  $dists = array_map(function($i) use($ponto){
    return Helper::dist($ponto,$i);
  },$ancoras);

  $indice = array_keys($dists, min($dists));

  return $indice[0]+1;
}
// FIM TESTE



private function getErroX(){
  if(!$this->erroX){
    $a1 = $this->image->ancoras[1]->getCentro();
    $a2 = $this->image->ancoras[2]->getCentro();
    // Correção de erro de escala em X
    $avaliado = bcsub($a2[0],$a1[0]);
    $esperado = bcmul($this->image->medidas['distAncHor'],$this->image->escala);
    $this->erroX = bcdiv(bcsub($avaliado,$esperado),$this->image->medidas['distAncHor']);
  }
  return $this->erroX;
}

private function getErroY(){
  if(!$this->erroY){
    $a1 = $this->image->ancoras[1]->getCentro();
    $a4 = $this->image->ancoras[4]->getCentro();
    // Correção de erro de escala em Y
    $avaliado = bcsub($a4[1],$a1[1]);
    $esperado = bcmul($this->image->medidas['distAncVer'],$this->image->escala);
    $this->erroY = bcdiv(bcsub($avaliado,$esperado),$this->image->medidas['distAncVer']);
  }
  return $this->erroY;
  // $erro = abs($erro);
  // echo 'ESC: ' . $this->image->escala . "\n";
  // echo 'ESP: ' . $esperado. "\n";
  // echo 'AVA: ' . $avaliado. "\n";
  // echo 'DIF: ' . ($avaliado - $esperado) . "\n";
  // echo 'ERR: ' . $erroX . "\n";
  // echo "X: " . $px . ' - Y: ' . $py . "\n";
  // echo "X: " . $px . ' - Y: ' . ($py + $d[2]*$erro) . "\n";
  // exit;
}



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
