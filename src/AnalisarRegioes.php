<?php

class AnalisarRegioes {
  const TipoELipse = 0;


  private $image;

  public $pontoBase=array(0,0);
  public $regioes=array();
  private $debug;

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
        throw new Exception('Tipo de região desconhecida.', 500);
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

    $a1 = $this->image->ancoras[1]->getCentro();
    $a2 = $this->image->ancoras[2]->getCentro();
    $a3 = $this->image->ancoras[3]->getCentro();
    $a4 = $this->image->ancoras[4]->getCentro();

    if($px > 0){
      $px += $a1[0];
      $base = $py > 0 ? $a1 : $a4;
    } else {
      $px += $a2[0];
      $base = $py > 0 ? $a2 : $a3;
    }

    if($py > 0){
      $py += $a1[1];
    } else {
      $py += $a4[1];
    }

    // Corre��o de erro de escala em Y
    $avaliado = $a4[1] - $a1[1];
    $esperado = $this->image->medidas['distAncVer'] * $this->image->escala;
    $erro = ($avaliado - $esperado) / $this->image->medidas['distAncVer'];

    // echo 'ESC: ' . $this->image->escala . "\n";
    // echo 'ESP: ' . $esperado. "\n";
    // echo 'AVA: ' . $avaliado. "\n";
    // echo 'DIF: ' . ($avaliado - $esperado) . "\n";
    // echo 'ERR: ' . $erro . "\n";
    // echo "X: " . $px . ' - Y: ' . $py . "\n";
    // echo "X: " . $px . ' - Y: ' . ($py + $d[2]*$erro) . "\n";
    // exit;
    $px += $d[1]*$erro;
    $py += $d[2]*$erro;

    $base = $a1;
    $p = [$px,$py];

    /// TESTES AJUSTE ESCALA
    // $escalaAvaliada = ($a4[1] - $py) / $this->image->medidas[4][1];



    // echo "ESC: " . $this->image->escala . '-' . $escalaAvaliada . "\n";
    // print_r($this->image->medidas['regioes'][0]);
    // echo $avaliado . '  -  ' . $esperado . ' | ' . $erro . "\n";

    // $npy = $escalaAvaliada * $this->image->medidas['regioes'][0][2] + $a1[1];

    // echo $py . " | " . $npy .  "\n";
    // exit;
    // $py = $npy;
    // print_r($avaliado);
    // echo "\n";
    // print_r($esperado);
    // echo "\n";
    // exit;
    // echo "ERRO POR PONTO: " . $erro . "\n";
    // echo "ERRO NO PONTO : " . ($erro * ($p[1] - $a1[1]) );
    // $distA1 = $p[1] - $a1[1];
    // echo $distA1 . "\n";
    // echO "\nPONTO DE->PARA: ";
    // echo $p[1] . ' - ';
    // $p[1] += ($erro *  ($p[1] - $a1[1]));
    // echo $p[1] . "\n";

    return $p;
    // return Helper::rotaciona($p,$a4,$this->image->rot);
  }

  private function interpretaElipse($id,$r,$e){
  $taxaPreenchimento = $this->getTaxaPreenchimento($e);

  $minMatch = isset($this->image->medidas['regioes'][$id][5]) ? $this->image->medidas['regioes'][$id][5] : PREENCHIMENTO_MINIMO;

  if(DEBUG){
    if($taxaPreenchimento >= $minMatch){
      imagefilledellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], imagecolorallocate($this->debugImage, 255, 255, 0));
    } else {
      imageellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], imagecolorallocate($this->debugImage, 255, 0, 0));
    }
  }

  return [
    $taxaPreenchimento,
    ($taxaPreenchimento >= $minMatch) ? $r[3] : $r[4],
  ];

}




  // public function exec(){
  //   $time = microtime(true);
  //   $regioes = [];
  //
  //   foreach ($this->regioes as $id => $r) {
  //     $tipo = $r[0];
  //     if($tipo == self::TipoELipse){ # 0:ELIPSE
  //       list($taxaPreenchimento,$retorno) = $this->interpretaElipse($id,$r);
  //       $regioes[$id] = [
  //         $retorno,
  //         $taxaPreenchimento,
  //         $this->pontoBase[0]+$r[1],
  //         $this->pontoBase[1]+$r[2],
  //       ];
  //     } else {
  //       throw new Exception('Tipo de região desconhecida.', 500);
  //     }
  //   }
  //
  //   $this->image->output['regioes'] = $regioes;
  //
  //   if(DEBUG) {
  //     Helper::cria($this->debugImage, 'ELIPISES_'.basename($this->image->arquivo));
  //     $this->image->saveTime('_analisarRegioes', $time);
  //   }
  // }


  // private function interpretaElipse($id,$r){
  //   $ponto = array($this->pontoBase[0]+$r[1],$this->pontoBase[1]+$r[2]);
  //   $e = Helper::rotaciona($ponto,$this->pontoBase,$this->image->rot);
  //   $taxaPreenchimento = $this->getTaxaPreenchimento($e);
  //
  //   $minMatch = isset($this->image->medidas['regioes'][$id][5]) ? $this->image->medidas['regioes'][$id][5] : PREENCHIMENTO_MINIMO;
  //
  //   if(DEBUG){
  //     if($taxaPreenchimento >= $minMatch){
  //       imagefilledellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], imagecolorallocate($this->debugImage, 255, 255, 0));
  //     } else {
  //       imageellipse($this->debugImage, $e[0], $e[1], $this->image->distancias['elpLargura'], $this->image->distancias['elpAltura'], imagecolorallocate($this->debugImage, 255, 0, 0));
  //     }
  //   }
  //
  //   return [
  //     $taxaPreenchimento,
  //     ($taxaPreenchimento >= $minMatch) ? $r[3] : $r[4],
  //   ];
  //
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
    $np = $sp = [];
    $areaAnalisada = 0;
    // echo count($pontos) . ' | ';
    foreach ($pontos as $x => $colunas) {
      foreach ($colunas as $y => $v) {
        if($elipse($x,$y)){
          $sp[$x][$y] = 1;
          $areaAnalisada++;
        } else {
          $np[$x][$y] = 1;
        }
      }
    }
    #Helper::pintaPontos($image, $sp, 'PONTOS___' . microtime(true), [255, 0, 0]);

    $area = pi() * ($elpA/2) * ($elpB/2);
    #echo "\n" . number_format( ($areaAnalisada * 100) / $area ,0) . '|';
    return ($areaAnalisada / $area);
  }

}
