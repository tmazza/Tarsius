<?php
$template = <<<TEMPLATE
<?php
# em milimetros
return array(
	'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2

	'ancora1' => array('{$ax}','{$ay}'),

	'distAncHor' => {$distAncHor},
	'distAncVer' => {$distAncVer},
	'elpAltura' => 2.5,
  	'elpLargura' => 4.36,

	'regioes' => array(
{$renderRegioes()}
	),
	'formatoSaida' => {$formatoSaida},
);
TEMPLATE;

return $template;