<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\Object;

/**
 * Gera assinautra de um objeto. 
 *
 * Para geração da assinatura é necessário somente as coordenadas dos pontos que 
 * compõem o objeto, sem os pontos de background.
 *
 * @todo linkar artigo com implementação.
 */
class Signature
{
	/**
	 * @var static $l Raio do objeto. Maior distância entre o centro do objeto
	 * e uma de suas bordas.
	 */
	private $l; // Raio do objeto
	/**
	 * @var static $n Quantidade de circulos internos
	 */
	private static $n = 18; // 
	/**
	 * @var static $n Quantidade de cortes radiais
	 */
	private static $m = 180;

	/**
	 * Gera representação em coordenadas polares do objeto
	 * @param Object $object
	 *
	 * @return bool[][] Matrix com pontos e situação
	 */
	public static function generate($object)
	{
		list($xc, $yc) = $object->getCenter();
		$points = $object->getPoints();
		$this->l = $object->getRadius();

		$ps = array();
		foreach ($points as $p) {
			$ps[$p[0] . '-' . $p[1]] = $p[0] . '-' . $p[1];
		}

		$points = array();
		$matrix = array();
		for ($i = 0; $i < self::$n; $i++) {
			for ($j = 0; $j < self::$m; $j++) {
				$r = floor(($i * $this->l) / (self::$n - 1));
				$ang = $j * (360 / self::$m);
				$x = ceil($r * cos($ang)) + $xc;
				$y = ceil($r * sin($ang)) + $yc;

				$matrix[$i][$j] = isset($ps[$x . '-' . $y]);
				if (isset($ps[$x . '-' . $y])) {
					$points[$x][$y] = true;
				}
			}
		}

//		Helper::pintaPontos(imagecreatetruecolor(2000, 2000), $pontos, 'OBJ' . microtime(true), [255, 255, 255]);
//		Helper::printMatizBinaria(self::$n, self::$m, $matrix);

		return $matrix;
	}

	/**
	 * Compara duas representações em coordenadas polares do objeto.
	 * @param type $ass1
	 * @param type $ass2
	 * @param type $ang
	 * @return type
	 */
	public static function comparaFormas($ass1, $ass2, $ang = 0) {
		$s = 0;
		for ($i = 0; $i < self::$n; $i++) {
			for ($j = 0; $j < self::$m; $j++) {
				$jRot = (($j + $ang) % self::$m);
				$s += ($ass1[$i][$jRot] xor $ass2[$i][$j]) ? 1 : 0;
			}
		}
		return 1 - ($s / ((self::$m / 2) * self::$n));
	}

	/**
	 * Visualiza representação em coordenadas polares do objeto.
	 * @param type $matrix
	 */
	public static function ver($matrix) {
		echo '<pre>';
		foreach ($matrix as $linha => $colunas) {
			foreach ($colunas as $coluna => $bin) {
				if ($matrix[$linha][$coluna]) {
					echo '0';
				} else {
					echo '-';
				}
			}
			echo '<br>';
		}
	}

}
