<?php

/**
 * Description of Objeto
 *
 * @author tiago.mazzarollo
 */
class Objeto {

    private $area = null;
    private $centro = null;
    private $maiorRaio = null;
    private $pontos;

    /**
     * Adiciona par ($x, $y) para conjunto de pontos do objeto
     * @param type $x
     * @param type $y
     */
    public function addPonto($x, $y) {
        $this->pontos[] = array($x, $y);
    }

    /**
     * Retorna conjunto de pontos do objeto
     * @return type
     */
    public function getPontos() {
        return $this->pontos;
    }

    /**
     * Retorna área (Qtd. de pontos).
     * @return type
     */
    public function getArea() {
        if (is_null($this->area)) {
            $this->area = count($this->pontos);
        }
        return $this->area;
    }

    /**
     * Calcula o centro de massa/gravidade.
     * @param type $obj
     * @return type
     */
    public function getCentro() {
        if (!isset($this->centro)) {
            $somaX = $somaY = 0;
            foreach ($this->pontos as $p) {
                $somaX += $p[0];
                $somaY += $p[1];
            }
            $this->centro = [ceil($somaX / $this->area), ceil($somaY / $this->area)];
        }
        return $this->centro;
    }

    /**
     * Sobreescreve posição da âncora
     */
    public function setCentro($ponto){
        $this->centro = $ponto;
    }

    /**
     * Calcula o maior raio.
     * TODO: otimizar. Sendo usado algoritmo ingênuo. 
     * A partir do centro calcula a distancia entre todos os pontos do 	 * objeto, a maior distância é selecionada.
     * @param type $obj
     * @return type
     */
    public function getMaiorRaio() {
        if (is_null($this->maiorRaio)) {
            $dists = array();
            $centro = $this->getCentro();
            foreach ($this->getPontos() as $p) {
                $dist = sqrt(pow($centro[0] - $p[0], 2) + pow($centro[1] - $p[1], 2));
                $dists[$p[0] . '-' . $p[1]] = $dist;
            }
            arsort($dists);
            $this->maiorRaio = array_shift($dists);
        }
        return $this->maiorRaio;
    }

}
