<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

trait Helper
{

    /**
     * Rotaciona pixel de acordo com do angulo de rotação $ang
     *
     * @param array $ponto Ponto a ser rotacionado
     * @param type $m
     *
     * @return array Ponto rotacionado
     */
    public static function rotatePoint($point, $referencePoint, $angle) {
        $x0 = $referencePoint[0];
        $y0 = $referencePoint[1];
        return [
            ($point[0]-$x0)*cos($angle) - ($point[1]-$y0)*sin($angle) + $x0,
            ($point[0]-$x0)*sin($angle) + ($point[1]-$y0)*cos($angle) + $y0,
        ];
    }

}