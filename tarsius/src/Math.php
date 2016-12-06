<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

trait Math
{

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
        $deltaX = bcsub($p1[0], $p2[0], 14);
        $deltaY = bcsub($p1[1], $p2[1], 14);
        return bcsqrt(bcmul($deltaX, $deltaX, 14) + bcmul($deltaY, $deltaY, 14), 14);
    }

    /**
     * Retorna o ponto médio entre dois pontos
     */
    public function getMidPoint($p1, $p2)
    {
        $minX = min($p1[0],$p2[0]); $maxX = max($p1[0],$p2[0]);
        $minY = min($p1[1],$p2[1]); $maxY = max($p1[1],$p2[1]);
        return [
            (($maxX - $minX) / 2) + $minX,
            (($maxY - $minY) / 2) + $minY,
        ];

    }

}