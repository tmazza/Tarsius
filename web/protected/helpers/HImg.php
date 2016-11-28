<?php

class HImg {

	public static function rotate($fileName,$angulo,$cor='#00000000')
	{
		$imagick = new \Imagick($fileName);
	    $imagick->rotateimage(new ImagickPixel($cor), $angulo);
	    $imagick->writeImage($fileName);
	}

}