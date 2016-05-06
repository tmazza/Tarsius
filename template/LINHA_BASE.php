<?php
// $ajusteX = 0;
$ajusteX = 0;
// $ajusteY = 0;
$ajusteY = 0;

$a1x = 13.0;
$a1y = 10.6;
// $a1x = 0;
// $a1y = 0;
$distAncHor = 187;
$distAncVer = 274;
// 11.811023622047
# em milimetros
return array(
	'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2

	'ancora1' => array($a1x,$a1y),

	'distAncHor' => $distAncHor,
	'distAncVer' => $distAncVer,
	# devem ser procisas
	'disElipHor' => 4.75,
	'disElipVer' => 71.55,

	'elpAltura' => 2.5,
  'elpLargura' => 4.36,

	'regioes' => array( # distancias relativas a ancora 1
			array(0,20-$a1x,20-$a1y,'A','W'),
			array(0,20-$a1x,90-$a1y,'A','W'),
			array(0,20-$a1x,270-$a1y,'A','W'),
			// array(0,20-$a1x,-84.5,'A','W'),
			// array(0,20-$a1x,-4.5,'A','W'),

			array(0,80-$a1x,20-$a1y,'A','W'),
			array(0,80-$a1x,90-$a1y,'A','W'),
			array(0,80-$a1x,270-$a1y,'A','W'),
			array(0,80-$a1x,270-$a1y,'A','W'),
	),
);
