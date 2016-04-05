<?php
// $ajusteX = 0;
$ajusteX = 0;
// $ajusteY = 0;
$ajusteY = 0;
$a1x = 11.6;
$a1y = 0.9;

# em milimetros
return array(
	'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2
	'ancora1' => array($a1x,$a1y),
	// 'ancora1' => array(1.6088060965284,1.6088060965284),
	'distAncHor' => 187,
	'distAncVer' => 274,
	# devem ser procisas
	'disElipHor' => 4.75,
	'disElipVer' => 71.55,

	'elpAltura' => 2.5,
  'elpLargura' => 4.36,

	'diagonal' => 332.05,
	'code_template' => array(103.5,0,126,3.5),
	'regioes' => array( # distancias relativas a ancora 1


	array(0,20-$a1x,20-$a1y,'A','W'),
	array(0,40-$a1x,20-$a1y,'B','W'),
	array(0,60-$a1x,20-$a1y,'C','W'),
	array(0,80-$a1x,20-$a1y,'D','W'),


	),
);
