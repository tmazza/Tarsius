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
 * Implementação do algoritmo explicada no artigo disponível no link acima.
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
     * @todo Documentar!!!
     */
    private $labelsPontos = [];

    /**
     * Considera somente objetos com área maior que $area.
     * 
     * @param int $area Área mínima para ser considerado como objeto.
     */
    public function setMinArea(int $area)
    {
        $this->minArea = $area;
    }

    /**
     * Considera somente objetos com área menor que $area.
     * 
     * @param int $area Área máxima para ser considerado como objeto.
     */
    public function setMaxArea(int $area)
    {
        $this->maxArea = $area;
    }

    /**
     * Função principal. Executa todas as etapas para obtenção dos componentes
     * conexos do conjunto de pontos.
     *
     * @param array[] $pontos Pontos de foreground(pretos) quer serão processados.
     */
    public function getObjects(array $points)
    {
        $this->get($points);

        # TODO: verificar qual a ordem correta de aplicação!!
        $this->applyAreaFilters();
        $this->reorganizaLabels();
        return $this->objects;
    }

    /**
     * A Linear-time two-scan labbeling algorithn
     * @link http://ieeexplore.ieee.org/stamp/stamp.jsp?tp=&arnumber=4379810 Lifeng He, Yuyan Chao and Kenji Suzuki
     */
    private function get($pontos)
    {

        # seguir refatoração daqui!
        $labels = 1;

        $e_l = $t_l = array();

        foreach ($pontos as $x => $linha) {
            foreach ($linha as $y => $p) {
                $this->setLabelDePonto($x, $y, $labels, $e_l, $t_l);
            }
        }

        foreach ($this->labelsPontos as $x => $linha) {
            foreach ($linha as $y => $l) {
                $label = $t_l[$l];
                if (!isset($this->objects[$label])) {
                    $this->objects[$label] = new Object();
                }
                $this->objects[$label]->addPonto($x, $y);
            }
        }
    }

    /**
     * Define o label do ponto.
     * Se somente o ponto superior ou somente o ponto a esquerda possui um label, este é usado.
     * Se o ponto superior e o ponto a esquerda possuem label e são iguais, este é usado.
     * Se o ponto superior e o ponto a esquerda possuem label e são distintos, os labels são marcados como equivalentes e o maior label é atribuido ao ponto.
     * Se nenhum dos pontos (superior e esquerdo) possuem label, um novo é criado.
     * @param int $x
     * @param int $y
     */
    protected function setLabelDePonto($x, $y, &$labels, &$e_l, &$t_l) {
        $posiveis = $this->getLabelsNaMascara($x, $y);

        $qtdPossiveis = count($posiveis);
        if ($qtdPossiveis == 0) { // Novo label!
            $label = $labels;
            if (!isset($e_l[$label])) {
                $e_l[$label] = array();
            }
            $e_l[$label][] = $label;
            $t_l[$label] = $label;
            $labels++;
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

        $this->labelsPontos[$x][$y] = $label;
    }

    private function resolve($old, $new, &$t_l, &$e_l) {
        // Percorre labels contidos em S(v) alterando T(l) = u
        foreach ($e_l[$old] as $l) {
            $t_l[$l] = $new;
        }
        // Junta arrays
        $e_l[$new] = array_merge($e_l[$new], $e_l[$old]);
        // Acaba com array de v
        unset($e_l[$old]);
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



    protected function reorganizaLabels() {
        $labels = array_flip(array_keys($this->objects));
        foreach ($this->objects as $label => $obj) {
            if (isset($labels[$label])) {
                $this->objects[$labels[$label]] = $obj;
                unset($this->objects[$label]);
            }
        }
    }

    protected function agrupaPontosDeObjetos() {
        foreach ($this->labelsPontos as $ponto => $label) {
            list($x, $y) = explode('-', $ponto);
            if (!isset($this->objects[$label])) {
                $this->objects[$label] = new Object();
            }
            $this->objects[$label]->addPonto($x, $y);
        }
    }

    /**
     *  Define relação de equivalencia entre os labels
     */
    protected function filtraLabelsEquivalentes($labelsEquivalentes) {

        $equivalentes = array_unique($labelsEquivalentes);
        $eqFiltrados = array();
        $qtdTotal = count($equivalentes); // Para n�o faze o count() a cada loop
        $count = 0;
        while ($count < $qtdTotal) {
            $par = array_shift($equivalentes);

            list($me, $ma) = explode('-', $par);

            if (!isset($eqFiltrados[$me])) {
                $eqFiltrados[$me] = array();
            }
            $eqFiltrados[$me][] = (int) $ma;

            // Percorre todo o array substituindo $ma
            array_walk($equivalentes, function(&$item) use($me, $ma) {
                list($me2, $ma2) = explode('-', $item);
                $me2 = $me2 == $ma ? $me : $me2;
                $ma2 = $ma2 == $ma ? $me : $ma2;
                $item = min($me2, $ma2) . '-' . max($me2, $ma2);
            });
            $count++;
        }

        return $eqFiltrados;
    }

    /**
     *  Altera os labels atribuidos no primeiro processo de acordo como a equivalencia de labels
     */
    protected function redistribuiLabels($equivalentes) {
        foreach ($equivalentes as $l => $eqv) {
            $eqv = array_unique($eqv);
            $fliped = array_flip($eqv);
            foreach ($this->labelsPontos as $ponto => $label) {
                if (isset($fliped[$label])) {
                    $this->labelsPontos[$ponto] = $l;
                }
            }
        }
    }

    /**
     * Mascara excutada para cada pixel existente.
     * @param type $x
     * @param type $y
     * @return type
     */
    private function getLabelsNaMascara($x, $y) {
        $left = isset($this->labelsPontos[$x - 1][$y]) ? $this->labelsPontos[$x - 1][$y] : false;
        $topLeft = isset($this->labelsPontos[$x - 1][$y - 1]) ? $this->labelsPontos[$x - 1][$y - 1] : false;
        $top = isset($this->labelsPontos[$x][$y - 1]) ? $this->labelsPontos[$x][$y - 1] : 0;
        $topRight = isset($this->labelsPontos[$x + 1][$y - 1]) ? $this->labelsPontos[$x + 1][$y - 1] : false;

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

}
