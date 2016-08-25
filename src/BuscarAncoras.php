<?php

class BuscarAncoras {

  private $image;

  public function __construct($image){
    $this->image = $image;
  }

  public function exec(){
    $this->run();
  }

  private function run(){
    # ANCORA 1
    $this->image->ancoras[1] = $this->getAncora(1, $this->image->distancias['ancora1']);

    # ANCORA 2
    $this->image->buscador->setTolerancia($this->image->ancoras[1]->getArea()); // Atualiza tolerância de busca de ancora baseado na área encontrada para a ancora 1
    $this->image->buscador->areaBuscaInicial = ($this->image->ancoras[1]->getMaiorRaio() * 3); // Adapta tamanho da área de busca em relação ao raio da ancora
    $this->image->ancoras[2] = $this->getAncora(2, $this->posicaoEsperadaAncora2());
    # ROTACAO: Define angulo de rotação baseado em ancora 1 e 2
    $this->image->rot = atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[2]->getCentro()));

    # ANCORA 3
    $this->image->ancoras[3] = $this->getAncora(3, $this->posicaoEsperadaAncora3());

    # ANCORA 4
    $this->image->ancoras[4] = $this->getAncora(4, $this->posicaoEsperadaAncora4());
    # ROTACAO:TODO: quando usar? Localiza quarta ancora
    $novaRotacao = atan($this->calcCoefReta($this->image->ancoras[1]->getCentro(), $this->image->ancoras[4]->getCentro(), true));
    $this->image->rot = ($this->image->rot + $novaRotacao) / 2;
  }


  /**
   * Localiza ancora iniciando busca em $ponto base comparando com $tipoAncora
   * @param type $tipoAncora
   * @param type $pontoEsperado
   * @return \Objeto
   * @throws Exception
   */
  public function getAncora($tipoAncora, $pontoEsperado) {
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
    return Helper::calcCoefReta($p0,$p1,$inverse);
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
