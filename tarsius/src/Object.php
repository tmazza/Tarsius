<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * Mantem pontos e propriedades de uma objeto da imagem.
 */
class Object
{

    /**
     * @var int[][] Conjunto de pontos do objeto.
     */
    private $points;
    /**
     * @var int $area Área do objeto.
     */
    private $area = false;
    /**
     * @var int[][] $centro Ponto de centro de massa do objeto.
     */
    private $centro = false;
    /**
     * @var int $maiorRaio Maior raio entre o centro de massa e as extremidades do objeto.
     */
    private $maiorRaio = false;
    /**
     * @var int[][] $signature Assniatura do objeto conforme implementado em Signature
     */
    private $signature = false;

    /**
     * Adiciona par ($x, $y) para conjunto de pontos do objeto
     *
     * @todo fazer cálcula da área enquanto pontos são adicionados
     *
     * @param int $x Coordenada x do ponto
     * @param int $y Coordenada y do ponto
     */
    public function addPoint($x, $y)
    {
        $this->points[] = [$x, $y];
    }

    /**
     * Retorna conjunto de pontos do objeto
     *
     * @return int[][] Conjunto de pontos da imagem
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Soma quantidade de pontos do objeto da imagem.
     *
     * @return int área do objeto
     */
    public function getArea()
    {
        if (!$this->area) {
            $this->area = count($this->points);
        }
        return $this->area;
    }

    /**
     * Calcula o centro de massa/gravidade do objeto.
     *
     * @return int[][] Centro de massa do objeto
     */
    public function getCenter()
    {
        if (!$this->centro) {
            $area = $this->getArea();
            $somaX = $somaY = 0;
            foreach ($this->points as $p) {
                $somaX += $p[0];
                $somaY += $p[1];
            }
            $this->centro = [ceil($somaX / $area), ceil($somaY / $area)];
        }
        return $this->centro;
    }

    /**
     * Altera o valor do centro do objeto
     *
     * @param array $center
     */
    public function setCenter($center)
    {
        $this->centro = $center;
    }


    /**
     * Calcula o maior raio.
     * A partir do centro calcula a distancia entre todos os pontos do   * objeto, a maior distância é selecionada.
     *
     * @todo reduzir complexida do algoritmo.
     *
     * @return type
     */
    public function getRadius()
    {
        if (!$this->maiorRaio) {
            $dists = array();
            list($xc, $yc) = $this->getCenter();
            foreach ($this->getPoints() as $p) {
                list($x, $y) = $p;
                $dist = sqrt(pow($xc - $x, 2) + pow($yc - $y, 2));
                $dists[$x . '-' . $y] = $dist;
            }
            arsort($dists);
            $this->maiorRaio = array_shift($dists);
        }
        return $this->maiorRaio;
    }

    /**
     * Retornar a assinatura da imagem
     */
    public function getSignature()
    {
        if (!$this->signature) {
            $this->signature = Signature::generate($this);
        }
        return $this->signature;
    }


}
