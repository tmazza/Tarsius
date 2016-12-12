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
	private static $l; // Raio do objeto
	/**
	 * @var static $n Quantidade de circulos internos
	 *
	 * @todo deveria variar de acordo com o tamanho ou formato do objeto?!
	 */
	private static $n = 16;
	/**
	 * @var static $n Quantidade de cortes radiais
	 *
	 * @todo deveria variar de acordo com o tamanho ou formato do objeto?!
	 */
	private static $m = 128;

	/**
	 * Gera representação em coordenadas polares do objeto
	 * @param Object $object
	 *
	 * @return bool[][] Matrix com assinatura da imagem
	 */
	public static function generate(Object $object)
	{
		list($xc, $yc) = $object->getCenter();
		$points = $object->getPoints();
		self::$l = $object->getRadius();

		$ps = [];
		foreach ($points as $p) {
			$ps[$p[0] . '-' . $p[1]] = $p[0] . '-' . $p[1];
		}

		$points = $matrix = [];
		for ($i = 0; $i < self::$n; $i++) {
			for ($j = 0; $j < self::$m; $j++) {
				$r = floor(($i * self::$l) / (self::$n - 1));
				$ang = $j * (360 / self::$m);
				$x = ceil($r * cos($ang)) + $xc;
				$y = ceil($r * sin($ang)) + $yc;

				$matrix[$i][$j] = isset($ps[$x . '-' . $y]);
				// if ($matrix[$i][$j]) {
				// 	$points[$x][$y] = true;
				// }
			}
		}

        // # DEBUG
        // if (Tarsius::$enableDebug) {
        // 	$image = imagecreatetruecolor(1000, 1000);
        // 	$rgb = [255, 0, 0];
        //     foreach ($points as $x => $ys) {
        //     	foreach ($ys as $y => $nop) {
        //     		if ($nop) {
		      //   		imagesetpixel($image, $x, $y, imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]));
        //     		}
        //     	}      
        //     }      
        //     imagejpeg($image, __DIR__ . '/debug/' . microtime(true) . '_' . rand(0,100) . '_signature.jpg');
        // }

		return $matrix;
	}

	/**
	 * Compara duas representações em coordenadas polares do objeto.
	 *
	 * @param array[][] $signature1 Matrix no formato de retorno de generate()
	 * @param array[][] $signature2 Matrix no formato de retorno de generate()
	 * @param type $angle Ângulo de rotação ser considerado na comparação
	 *
	 * @return float Taxa de semelhanças entre duas assinaturas
	 */
	public static function compare($signature1, $signature2, $angle = 0)
	{
		$s = 0;
		for ($i = 0; $i < self::$n; $i++) {
			for ($j = 0; $j < self::$m; $j++) {
				$jRot = (($j + $angle) % self::$m);
				$s += ($signature1[$i][$jRot] xor $signature2[$i][$j]) ? 1 : 0;
			}
		}
		return 1 - ($s / ((self::$m / 2) * self::$n));
	}

	/**
	 * # DEBUG
	 * Print representação em coordenadas polares do objeto.
	 *
	 * @todo possibilitar geração de uma iamgem com o formato do objeto
	 * e somente os pontos que foram considerados
	 *
	 */
	public static function printSignature($matrix)
	{
		echo "\n";
		foreach ($matrix as $linha => $colunas) {
			foreach ($colunas as $coluna => $bin) {
				echo $matrix[$linha][$coluna] ? '|' : '-';
			}
			echo "\n";
		}
		echo "\n";
	}

}
