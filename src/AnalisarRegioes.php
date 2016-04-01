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

    echo "\n\n";
    $regioes = [];


    foreach ($this->regioes as $id => $r) {
      $tipo = $r[0];
      if($tipo == self::TipoELipse){ # 0:ELIPSE
        list($taxaPreenchimento,$retorno) = $this->interpretaElipse($id,$r);
        $regioes[$id] = [
          $retorno,
          $taxaPreenchimento,
          $this->pontoBase[0]+$r[1],
          $this->pontoBase[1]+$r[2],
        ];
      } else {
        throw new Exception('Tipo de região desconhecida.', 500);
      }
    }
    // echo '<pre>';
    // print_r($regioes->regioes);
    // exit;
    $this->image->output['regioes'] = $regioes;

    if(DEBUG) {
      Helper::cria($this->debugImage, 'ELIPISES_'.basename($this->image->arquivo));
      $this->image->saveTime('_analisarRegioes', $time);
    }
  }


  private function interpretaElipse($id,$r){
    $e = Helper::rotaciona(array($this->pontoBase[0]+$r[1],$this->pontoBase[1]+$r[2]),$this->pontoBase,$this->image->rot);
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
