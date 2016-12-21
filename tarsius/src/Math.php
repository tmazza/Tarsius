<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

trait Math
{

    /**
     * Converte milímetros para pixel, considerando a resolução da imagem.
     * @param mixed $data int ou array
     * @param float $scale escala ser aplicada
     *
     * @return valor(es) em pixel.  
     */
    public function applyResolution($data, $scale)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->applyResolution($v, $scale);
            }
        } else {
            $data = $data * $scale;
        }
        return $data;
    }

    /**
     * Rotaciona pixel de acordo com do angulo de rotação $ang
     *
     * @param array $ponto Ponto a ser rotacionado
     * @param type $m
     *
     * @return array Ponto rotacionado
     */
    public function rotatePoint($point, $referencePoint, $angle)
    {
        $x0 = $referencePoint[0];
        $y0 = $referencePoint[1];
        return [
            ($point[0]-$x0)*cos($angle) - ($point[1]-$y0)*sin($angle) + $x0,
            ($point[0]-$x0)*sin($angle) + ($point[1]-$y0)*cos($angle) + $y0,
        ];
    }

    /**
     * Cálculo do coeficiente da reta que passa por doi pontos
     */
    public function lineGradient($p1, $p2, $reverse = false)
    {
        if ($reverse) {
            return (($p1[0] - $p2[0]) / ($p1[1] - $p2[1])) * -1;
        } else {
            return ($p2[1] - $p1[1]) / ($p2[0] - $p1[0]);
        }
    }

    /**
     * Cálculo da distãncia entre dois pontos
     */
    public function distance($p1, $p2)
    {
        $deltaX = $p1[0] - $p2[0];
        $deltaY = $p1[1] - $p2[1];
        return sqrt($deltaX*$deltaX + $deltaY*$deltaY);
    }

    /**
     * Retorna o ponto médio entre dois pontos
     */
    public function getMidPoint($p1, $p2, $width = false, $height = false)
    {
        $minX = min($p1[0],$p2[0]); $maxX = max($p1[0],$p2[0]);
        $minY = min($p1[1],$p2[1]); $maxY = max($p1[1],$p2[1]);
        
        $minRateX = $maxRateX = 0.5;
        $minRateY = $maxRateY = 0.5;
        if(Tarsius::$dynamicPointReference) {
            # Controle eixo x considerando disntância para âncora mais próxima
            if ($width) {
                $maxRateX = $minX / $width;
                $minRateX = 1 - $maxRateX;
            }
            # Controle eixo x considerando disntância para âncora mais próxima
            if ($height) {
                $maxRateY = $minY / $height;
                $minRateY = 1 - $maxRateY;
            }
        }

        $x = $minRateX*$minX + $maxRateX*$maxX;
        $y = $minRateY*$minY + $maxRateY*$maxY;

        return [$x, $y];

    }

}