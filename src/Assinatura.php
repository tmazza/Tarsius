<?php

/**
 * Description of Assinatura
 *
 * @author tiago.mazzarollo
 */
class Assinatura {

	// Rever valores de comparação, pois parecem não incluenciar muito no resultado final da analise
	public static $l; // Raio do objeto
	public static $n = 18; // Quantidade circulos internos MAIOR FIGURA COMAPRA � UM QUADRADO DE 50 X 50 => RAIO < 71
	public static $m = 180; // Quantidade de angulos 10º

	/**
	 * Gera representação em coordenadas polares do objeto
	 * @param type $obj
	 * @return type
	 */
	public static function get($obj) {
		$orto = $obj->getCentro();
		$pontos = $obj->getPontos();
		self::$l = $obj->getMaiorRaio();

		$ps = array();
		foreach ($pontos as $p) {
			$ps[$p[0] . '-' . $p[1]] = $p[0] . '-' . $p[1];
		}

		$pontos = array();
		$matrix = array();
		for ($i = 0; $i < self::$n; $i++) {
			for ($j = 0; $j < self::$m; $j++) {
				$r = floor(($i * self::$l) / (self::$n - 1));
				$ang = $j * (360 / self::$m);
				$x = ceil($r * cos($ang)) + $orto[0];
				$y = ceil($r * sin($ang)) + $orto[1];

				$matrix[$i][$j] = isset($ps[$x . '-' . $y]);
				if (isset($ps[$x . '-' . $y])) {
					$pontos[$x][$y] = true;
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
