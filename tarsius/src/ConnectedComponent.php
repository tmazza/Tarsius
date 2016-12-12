<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\Object;

/**
 *
 * A Linear-time two-scan labbeling algorithm.
 * @link http://ieeexplore.ieee.org/stamp/stamp.jsp?tp=&arnumber=4379810 Lifeng He, Yuyan Chao and Kenji Suzuki
 *
 * Implementação do algoritmo no artigo disponível no link acima. Esta implementação é um vesrão
 * ADAPTADA do artigo.
 *
 * Nesta implmentação são utilizados somente os pontos de foreground os quais são mantidos 
 * em uma matrix indexada pelas posição x,y do ponto da imagem.
 *
 * O retorno é uma lista tendo o label(o representante do label set), como chave e como 
 * valor o objeto(instância class Object).
 *
 * @todo otimizar algoritmo de resolução dos labels
 *
 */
class ConnectedComponent
{

    /**
     * @var Object[] $objects Lista de objetos que respeitam os filtros definidos.
     */
    public $objects = [];
    /**
     * @var int $minArea Valor da área mínima a ser considerada.
     */
    private $minArea = false;
    /**
     * @var int $maxArea Valor da área máxima a ser considerada.
     */
    private $maxArea = false;

    /**
     * Considera somente objetos com área maior que $area.
     * 
     * @param int $area Área mínima para ser considerado como objeto.
     */
    public function setMinArea($area)
    {
        $this->minArea = $area;
    }

    /**
     * Considera somente objetos com área menor que $area.
     * 
     * @param int $area Área máxima para ser considerado como objeto.
     */
    public function setMaxArea($area)
    {
        $this->maxArea = $area;
    }

    /**
     * Função principal. Executa todas as etapas para obtenção dos componentes
     * conexos do conjunto de pontos.
     *
     * @param int[][] $pontos Pontos de foreground(pretos) quer serão processados.
     */
    public function getObjects($points)
    {

        list($finalLabels, $t_l) = $this->applyLabels($points);
        $this->groupObjects($finalLabels, $t_l);
        $this->applyAreaFilters();
        $this->renameLabels();

        return $this->objects;
    }

    /**
     * A Linear-time two-scan labbeling algorithn ADAPTADO
     * @link http://ieeexplore.ieee.org/stamp/stamp.jsp?tp=&arnumber=4379810 Lifeng He, Yuyan Chao and Kenji Suzuki
     *
     * @param int[][] $points Pontos de foreground(pretos) quer serão processados.
     *
     * @return int[][] Lista de points com o valor do label associado.
     */
    private function applyLabels($points)
    {
        $labelCount = 1;
        $e_l = $t_l = array();
        
        $finalLabels = [];

        # aplica label para cada ponto
        foreach ($points as $x => $linha) {
            foreach ($linha as $y => $p) {

                $posiveis = $this->getLabelsOfMask($x, $y, $finalLabels);

                $qtdPossiveis = count($posiveis);
                if ($qtdPossiveis == 0) { // Novo label!
                    $label = $labelCount;
                    if (!isset($e_l[$label])) {
                        $e_l[$label] = array();
                    }
                    $e_l[$label][] = $label;
                    $t_l[$label] = $label;
                    $labelCount++;
                } else {

                    if ($qtdPossiveis > 1) {
                        $label = $posiveis[0];
                        foreach ($posiveis as $p) {
                            $u = $t_l[$label];
                            $v = $t_l[$p];
                            if ($u != $v) {
                                if ($u < $v) {
                                    $this->resolve($v, $u, $t_l, $e_l);
                                } else {
                                    $this->resolve($u, $v, $t_l, $e_l);
                                }
                            }
                        }
                    }

                    $label = $posiveis[0];
                }

                $finalLabels[$x][$y] = $label;
            }
        }

        return [$finalLabels, $t_l];
    }

    /**
     * Máscara com os pixel que devem ser considerados para a definição de um
     * label para o pixel em avaliação
     * 
     * @param int &$x coordenada x do ponto na imagem
     * @param int &$y coordenada y do ponto na imagem
     * @param int[][] &$finalLabels conjunto de pontos com labels já atribuídos
     *
     * @return int[] conjunto de labels que podem ser do pixel em avaliaçõa.
     */
    private function getLabelsOfMask(&$x, &$y, &$finalLabels)
    {
        $left     = isset($finalLabels[$x - 1][$y])     ? $finalLabels[$x - 1][$y]     : false;
        $topLeft  = isset($finalLabels[$x - 1][$y - 1]) ? $finalLabels[$x - 1][$y - 1] : false;
        $top      = isset($finalLabels[$x][$y - 1])     ? $finalLabels[$x][$y - 1]     : false;
        $topRight = isset($finalLabels[$x + 1][$y - 1]) ? $finalLabels[$x + 1][$y - 1] : false;

        $posiveis = array();
        if ($left) {
            $posiveis[] = $left;
        }
        if ($top) {
            $posiveis[] = $top;
        }
        if ($topLeft) {
            $posiveis[] = $topLeft;
        }
        if ($topRight) {
            $posiveis[] = $topRight;
        }

        return $posiveis;
    }

    /**
     * Label Equivalence Resolving
     */
    private function resolve($old, $new, &$t_l, &$e_l)
    {
        # Percorre labels contidos em S(v) alterando T(l) = u
        foreach ($e_l[$old] as $l) {
            $t_l[$l] = $new;
        }
        # Junta arrays
        $e_l[$new] = array_merge($e_l[$new], $e_l[$old]);
        # Acaba com array de v
        unset($e_l[$old]);
    }

    /**
     * Agrupa pontos em objetos.
     *
     * @param int[][] &$finalLabels Conjunto de pontos com os labels que foram atribuídos.
     *
     */
    private function groupObjects(&$finalLabels, &$t_l)
    {
        foreach ($finalLabels as $x => $linha) {
            foreach ($linha as $y => $l) {
                $label = $t_l[$l];
                if (!isset($this->objects[$label])) {
                    $this->objects[$label] = new Object();
                }
                $this->objects[$label]->addPoint($x, $y);
            }
        }
    }


    /**
     * Aplica filtros de área mínima e área máxima, caso estejam definidos.
     */
    private function applyAreaFilters()
    {
        if ($this->minArea || $this->maxArea) {
           foreach ($this->objects as $label => $obj) {
                $area = $obj->getArea();
                if ($this->minArea && $area < $this->minArea) {
                    unset($this->objects[$label]);
                }
                if ($this->maxArea && $area > $this->maxArea) {
                    unset($this->objects[$label]);
                }
            }
        }
    }

    /**
     * Renumera labels dos obejtos de 0 a n-1, sendo n a quantidade de objetos.
     */
    protected function renameLabels()
    {
        $labelSet = array_flip(array_keys($this->objects));
    
        foreach ($this->objects as $newLabel => $obj) {
            if (isset($labelSet[$newLabel])) {
                $this->objects[$labelSet[$newLabel]] = $obj;
                unset($this->objects[$newLabel]);
            }
        }
    }

}