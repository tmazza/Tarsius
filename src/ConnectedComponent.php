<?php

/**
 * A Linear-time two-scan labbeling algorithm
 * http://ieeexplore.ieee.org/stamp/stamp.jsp?tp=&arnumber=4379810
 *
 * --- Características da implementação. ---
 * Para representação da imagem um array() contendo somente os pontos
 * de foreground é utilizado.
 * É gerado um array tendo o label, o representante do label set, como chabe e
 * o conjunto de pontos pertencente ao objeto conexo, em um array, como valor.
 *
 *
 * TODO: Busca matriz de pixels de forma mais eficiente
 * http://stackoverflow.com/questions/13791207/better-way-to-get-map-of-all-pixels-of-an-image-with-gd
 *
 *
 * @author Tiago M. de paula
 */
class ConnectedComponent {

	public $objetos = array(); // TODO: criar classe Objeto com propriedade de área, posição, etc.
	protected $img;
	protected $corte = CORTE_PRETO;
	protected $filtraAreaMinima = false;
	protected $areaMinima;
	protected $filtraAreaMaxima = false;
	protected $areaMaxima;
	protected $labelsPontos = array();
	private $jump = 1;

	/**
	 *
	 * @param type $area
	 */
	public function setAreaMinima($area) {
		$this->filtraAreaMinima = true;
		$this->areaMinima = $area;
		if (count($this->objetos) > 0) {
			$this->aplicaFiltroDeBuscaPorArea();
		}
	}

	public function clearAreaMinima() {
		$this->filtraAreaMinima = false;
		unset($this->areaMinima);
	}

	/**
	 *
	 * @param type $area
	 */
	public function setAreaMaxima($area) {
		$this->filtraAreaMaxima = true;
		$this->areaMaxima = $area;
		if (count($this->objetos) > 0) {
			$this->aplicaFiltroDeBuscaPorArea();
		}
	}

	public function clearAreaMaxima() {
		$this->filtraAreaMaxima = false;
		unset($this->areaMaxima);
	}

	/**
	 * Aplica todos os filtros de busca ativados
	 */
	protected function aplicaFiltrosDeBusca() {
		if ($this->filtraAreaMinima || $this->filtraAreaMaxima) {
			$this->aplicaFiltroDeBuscaPorArea();
		}
		$this->reorganizaLabels();
	}

	protected function reorganizaLabels() {
		$labels = array_flip(array_keys($this->objetos));
		foreach ($this->objetos as $label => $obj) {
			if (isset($labels[$label])) {
				$this->objetos[$labels[$label]] = $obj;
				unset($this->objetos[$label]);
			}
		}
	}

	/**
	 * Corte por área mínima
	 */
	protected function aplicaFiltroDeBuscaPorArea() {
		foreach ($this->objetos as $label => $obj) {
			$area = $obj->getArea();
			if ($this->filtraAreaMinima && $area < $this->areaMinima) {
				unset($this->objetos[$label]);
			}
			if ($this->filtraAreaMaxima && $area > $this->areaMaxima) {
				unset($this->objetos[$label]);
			}
		}
	}

	protected function agrupaPontosDeObjetos() {
		foreach ($this->labelsPontos as $ponto => $label) {
			list($x, $y) = explode('-', $ponto);
			if (!isset($this->objetos[$label])) {
				$this->objetos[$label] = new Objeto();
			}
			$this->objetos[$label]->addPonto($x, $y);
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

	public function getObjetos($pontos) {
		$this->labelsPontos = array();
		$this->objetos = array();
		$this->get($pontos);
		$this->aplicaFiltrosDeBusca();
		return $this->objetos;
	}

	private function get($pontos) {
//        $time = microtime(true);
		$labels = 1;
		// First scan
		$e_l = $t_l = array();

		foreach ($pontos as $x => $linha) {
			foreach ($linha as $y => $p) {
				$this->setLabelDePonto($x, $y, $labels, $e_l, $t_l);
			}
		}

		foreach ($this->labelsPontos as $x => $linha) {
			foreach ($linha as $y => $l) {
				$label = $t_l[$l];
				if (!isset($this->objetos[$label])) {
					$this->objetos[$label] = new Objeto();
				}
				$this->objetos[$label]->addPonto($x, $y);
			}
		}
	}

	/**
	 * Define o label do ponto.
	 * Se somente o ponto superior ou somente o ponto a esquerda possue uma label, este é usado.
	 * Se o ponto superior e o ponto a esquerda possuem label e são iguais, este é usado.
	 * Se o ponto superior e o ponto a esquerda possuem label e são distintos, os labels são marcados como equivalentes e o maior label é atribuido ao ponto.
	 * Se nenhum dos pontos (superior e esquerdo) possuem label, um novo é criado.
	 * @param type $x
	 * @param type $y
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
