<?php
$template = <<<TEMPLATE
<?php
# em milimetros
return array(
	'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2

	'ancora1' => array('{$ax}','{$ay}'),

	'distAncHor' => 187,
	'distAncVer' => 274,

	'disElipHor' => 4.75,
	'disElipVer' => 71.55,

	'elpAltura' => 2.5,
  	'elpLargura' => 4.36,

	'regioes' => array(
{$renderRegioes()}
	),
);
TEMPLATE;

return $template;