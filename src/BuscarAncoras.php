<?php

class BuscarAncoras {

  private $image;

  public function __construct($image){
    $this->image = $image;
  }

  public function exec(){
    // if(DEBUG){
    //   $time = microtime(true);
    //   $this->runDebug();
    //   $this->image->saveTime('_localizaAncoras', $time);
    // } else {
      $this->run();
    // }
  }

  private function run(){
    # ANCORA 1
    $this->image->ancoras[1] = $this->getAncora(1, $this->image->distancias['ancora1']);
    # ESCALA:Baseado no tamanho esperado do raio x tamanho real em pixel encontrado, infere a escala da imagem.
    $this->image->setEscala($this->image->ancoras[1]->getMaiorRaio() / $this->image->medidas['raioTriangulo']);
    # ANCORA 2
    $this->image->buscador->setTolerancia($this->image->ancoras[1]->getArea()); // Atualiza tolerância de busca de ancora baseado na área encontrada para a ancora 1
    $this->image->buscador->areaBuscaInicial = ($this->image->ancoras[1]->getMaiorRaio() * 3); // Adapta tamanho da área de busca em relação ao raio da ancora
    $this->image->ancoras[2] = $this->getAncora(2, $this->posicaoEsperadaAncora2());
    # ESCALA:Baseado no tamanho esperado do raio y tamanho real em pixel encontrado, inferir a escala da imagem.
    $distanciaEntreAncoras = $this->image->ancoras[2]->getCentro()[0] - $this->image->ancoras[1]->getCentro()[0];
    $this->image->setEscala($distanciaEntreAncoras / $this->image->medidas['distAncHor']);
    # ROTACAO: Define angulo de rotação baseado em ancora 1 e 2
    $this->image->rot = atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[2]->getCentro()));
    # ANCORA 3
    $this->image->ancoras[3] = $this->getAncora(3, $this->posicaoEsperadaAncora3());
    # ESCALA: baseada no tamanho da diagonal
    $a = ($this->image->ancoras[3]->getCentro()[1]-$this->image->ancoras[1]->getCentro()[1]);
    $b = ($this->image->ancoras[3]->getCentro()[0]-$this->image->ancoras[1]->getCentro()[0]);
    $h = sqrt(pow($a,2) + pow($b,2));
    $this->image->setEscala($h / $this->image->medidas['diagonal']);
    # ANCORA 4
    $this->image->ancoras[4] = $this->getAncora(4, $this->posicaoEsperadaAncora4());
    # ROTACAO:TODO: quando usar? Localiza quarta ancora
    $this->image->rot = ($this->image->rot + atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[4]->getCentro(), true))) / 2;
  }

  private function runDebug(){
    # ANCORA 1
    $time = microtime(true);
    $this->image->ancoras[1] = $this->getAncora(1, $this->image->distancias['ancora1']);
    $this->image->saveTime('__findAncora_1', $time);
    # ESCALA:Baseado no tamanho esperado do raio x tamanho real em pixel encontrado, infere a escala da imagem.
    $time = microtime(true);
    $this->image->setEscala($this->image->ancoras[1]->getMaiorRaio() / $this->image->medidas['raioTriangulo']);
    $this->image->saveTime('__infereEscala_por_raio', $time);
    # ANCORA 2
    $time = microtime(true);
    $this->image->buscador->setTolerancia($this->image->ancoras[1]->getArea()); // Atualiza tolerância de busca de ancora baseado na área encontrada para a ancora 1
    $this->image->buscador->areaBuscaInicial = ($this->image->ancoras[1]->getMaiorRaio() * 3); // Adapta tamanho da área de busca em relação ao raio da ancora
    $this->image->ancoras[2] = $this->getAncora(2, $this->posicaoEsperadaAncora2());
    $this->image->saveTime('__findAncora_2', $time);
    # ESCALA:Baseado no tamanho esperado do raio x tamanho real em pixel encontrado, inferir a escala da imagem.
    $time = microtime(true);
    $distanciaEntreAncoras = $this->image->ancoras[2]->getCentro()[0] - $this->image->ancoras[1]->getCentro()[0];
    $this->image->setEscala($distanciaEntreAncoras / $this->image->medidas['distAncHor']);
    $this->image->saveTime('__infereEscala_por_distancia', $time);
    # ROTACAO: Define angulo de rotação baseado em ancora 1 e 2
    $this->image->rot = atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[2]->getCentro()));
    # ANCORA 3
    $time = microtime(true);
    $this->image->ancoras[3] = $this->getAncora(3, $this->posicaoEsperadaAncora3());
    $this->image->saveTime('__findAncora_3', $time);
    # ESCALA: baseada no tamanho da diagonal
    $a = ($this->image->ancoras[3]->getCentro()[1]-$this->image->ancoras[1]->getCentro()[1]);
    $b = ($this->image->ancoras[3]->getCentro()[0]-$this->image->ancoras[1]->getCentro()[0]);
    $h = sqrt(($a**2) +($b**2));
    $this->image->setEscala($h / $this->image->medidas['diagonal']);
    # ANCORA 4
    $time = microtime(true);
    $this->image->ancoras[4] = $this->getAncora(4, $this->posicaoEsperadaAncora4());
    $this->image->saveTime('__findAncora_4', $time);
    # ROTACAO:TODO: quando usar? Localiza quarta ancora
    $this->image->rot = ($this->image->rot + atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[4]->getCentro(), true))) / 2;

  }



  /**
   * Localiza ancora iniciando busca em $ponto base comparando com $tipoAncora
   * @param type $tipoAncora
   * @param type $pontoEsperado
   * @return \Objeto
   * @throws Exception
   */
  private function getAncora($tipoAncora, $pontoEsperado) {
      $ancora = $this->image->buscador->find($this->image->image, $this->image->assAncoras[$tipoAncora], $pontoEsperado);
      if (!($ancora instanceof Objeto)) {
          throw new Exception('Ancora ' . $tipoAncora . ' não foi encontrada.', 500);
      }
      return $ancora;
  }

  /**
   * Calcula coeficiente angular com ponto ($x0, $y0) e ($x1, $y1)
   * @param type $x0
   * @param type $y0
   * @param type $x1
   * @param type $y1
   * @param type $inverse
   */
  private function calcCoefReta($p0, $p1, $inverse = false) {
      if ($inverse) {
          return (($p0[0] - $p1[0]) / ($p0[1] - $p1[1])) * -1;
      } else {
          return ($p1[1] - $p0[1]) / ($p1[0] - $p0[0]);
      }
  }
  /**
   * Posicao esperada de ancora 2, baseado na posicao da ancora 1 e na distância esperada.
   * @return type
   */
  private function posicaoEsperadaAncora2() {
      $posAncora1 = $this->image->ancoras[1]->getCentro();
      return array(
          $posAncora1[0] + $this->image->distancias['distAncHor'],
          $posAncora1[1],
      );
  }

  /**
   * Posicao esperada de ancora 3, baseado na posicao da ancora 1 e na distância esperada.
   * @return type
   */
  private function posicaoEsperadaAncora3() {
      $posAncora2 = $this->image->ancoras[2]->getCentro();
      return Helper::rotaciona(array(
                  $posAncora2[0],
                  $posAncora2[1] + $this->image->distancias['distAncVer'],
      ),$this->image->ancoras[1]->getCentro(),$this->image->rot);
  }

  /**
   * Posicao esperada de ancora 3, baseado na posicao da ancora 1 e na distância esperada.
   * @return type
   */
  private function posicaoEsperadaAncora4() {
      $posAncora1 = $this->image->ancoras[1]->getCentro();
      return Helper::rotaciona(array(
                  $posAncora1[0],
                  $posAncora1[1] + $this->image->distancias['distAncVer'],
      ),$this->image->ancoras[1]->getCentro(),$this->image->rot);
  }



}
