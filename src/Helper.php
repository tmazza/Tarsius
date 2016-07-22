<?php

/**
 * Description of Helpers
 *
 * @author tiago.mazzarollo
 */
class Helper {

	/**
	 * Retorna as cores RGB do pixel.
	 * @param type $img
	 * @param type $x
	 * @param type $y
	 * @return type
	 */
	public static function getRGB($img, $x, $y) {
		$rgb = @imagecolorat($img, $x, $y);
		if (is_numeric($rgb)) {
			return array(Helper::shift(16, $rgb), Helper::shift(8, $rgb), Helper::shift(0, $rgb));
		} else {
			return array(255, 255, 255);
		}
	}

	public static function shift($qtd, $rgb) {
		return ($rgb >> $qtd) & 0xFF;
	}

	/**
	 * Copia imagem
	 * @param type $image
	 * @return type
	 */
	public static function copia($image) {
		$copia = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagecopy($copia, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		return $copia;
	}

	public static function cria($image, $nome) {
		$baseDir = __DIR__ . '/../data';
		imagepng($image, $baseDir . '/runtime/' . $nome . '.png');
	}

	public static function rect($image, $x0, $y0, $x1, $y1, $nome) {
		$copia = Helper::copia($image);
		imagerectangle($copia, $x0, $y0, $x1, $y1, imagecolorallocate($copia, 255, 0, 0));
		Helper::cria($copia, $nome);
	}

	public static function pintaPontos($image, $pontos, $nome, $rgb = [255, 0, 0]) {
		$copia = Helper::copia($image);
		foreach ($pontos as $linha => $colunas) {
			foreach ($colunas as $coluna => $ponto) {
				Helper::pintaPx($copia, $linha, $coluna, $rgb);
			}
		}
		Helper::cria($copia, $nome);
		imagedestroy($copia);
	}

	public static function pintaObjetos($image, $objetos) {
		$copia = Helper::copia($image);
		foreach ($objetos as $obj) {
			$pontos = $obj->getPontos();
			$cor = [rand(0, 255), rand(0, 255), rand(0, 255)];
			foreach ($pontos as $ponto) {
				Helper::pintaPx($copia, $ponto[0], $ponto[1], $cor);
			}
		}
		Helper::cria($copia, 'OBJETOS_' . microtime());
		imagedestroy($copia);
	}

	public static function pintaPx(&$image, $x, $y, $rgb) {
		imagesetpixel($image, $x, $y, imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]));
	}

	public static function printMatizBinaria($m, $n, $pontos, $rgbTrue = [255, 255, 255], $rgbFalse = [0, 0, 0]) {
		$image = imagecreatetruecolor($m, $n);
		foreach ($pontos as $linha => $colunas) {
			foreach ($colunas as $coluna => $bin) {
				if ($bin) {
					Helper::pintaPx($image, $linha, $coluna, $rgbTrue);
				} else {
					Helper::pintaPx($image, $linha, $coluna, $rgbFalse);
				}
			}
		}
		Helper::cria($image, 'z_ASSINATURA_' . microtime(true));
		imagedestroy($image);
	}

	/**
	 * Carrega arquivo de imagem
	 */
	public static function load($file) {
			$ext = pathinfo($file,PATHINFO_EXTENSION);
			if($ext === 'jpg'){
				$image = @imagecreatefromjpeg($file);
			} else {
				throw new Exception("Imagem deve ser jpg.", 500);
			}
			if (!$image) {
					throw new Exception("Imagem '{$file}' não pode ser carregada.", 500);
			}
			return $image;
	}

	/**
	 * Desloca pixel de acordo com a tag do angulo de rotação $m
	 * @param type $ponto
	 * @param type $m
	 * @return type
	 */
	public static function rotaciona($p, $pontoBase, $ang) {
			$x0 = $pontoBase[0];
			$y0 = $pontoBase[1];

			return [
				($p[0] - $x0)*cos($ang) - ($p[1] - $y0)*sin($ang) + $x0,
				($p[0] - $x0)*sin($ang) + ($p[1] - $y0)*cos($ang) + $y0,
			];
	}
	public static function calcCoefReta($p0, $p1, $inverse = false) {
		if ($inverse) {
				return (($p0[0] - $p1[0]) / ($p0[1] - $p1[1])) * -1;
		} else {
				return ($p1[1] - $p0[1]) / ($p1[0] - $p0[0]);
		}
	}

	public static function angulo($p0,$p1,$inverse=false){
		return atan(self::calcCoefReta($p0, $p1, $inverse));
	}

	public static function dist($a,$b){
		return sqrt(($a[0]-$b[0])**2 + ($a[1]-$b[1])**2);
	}

	public static function distBC($a,$b){
		$deltaX = bcsub($a[0],$b[0],14);
		$deltaY = bcsub($a[1],$b[1],14);
		return bcsqrt(bcmul($deltaX,$deltaX,14) + bcmul($deltaY,$deltaY,14),14);
	}

}
