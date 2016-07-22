<?php
# em milimetros
return array(
	'raioTriangulo' => (4.5 * sqrt(2)) / 2, # diagonal / 2
	'ancora1' => array(10, 17.5),
	'distAncHor' => 127.5,
	'distAncVer' => 182.5,
	'disElipHor' => 1,
	'disElipVer' => 37,
	'elpAltura'  => 2.45,
  'elpLargura' => 4.55,
	'diagonal'   => 222,
	'code_template' => array(103.5,0,126,3.5),
	'regioes' => array( # distancias relativas a ancora 1
	  # FORMATO: '<id>' => [distancia horizontal, distancia vertical, saida caso marcado, saida caso não marcado]
		#'ausente' => array(0,7.8,9.3,'A','B',0.1),
	  # linha 0
		array(0,1,37,'A','W'),
		array(0,1+8.85,37,'B','W'),
		array(0,1+(2*8.85),37,'C','W'),
		array(0,1+(3*8.85),37,'D','W'),
		array(0,1+(4*8.85),37,'E','W'),
		# linha 1
		array(0,1					,37 + 4.58,'A','W'),
		array(0,1+8.85		,37 + 4.58,'B','W'),
		array(0,1+(2*8.85),37 + 4.58,'C','W'),
		array(0,1+(3*8.85),37 + 4.58,'D','W'),
		array(0,1+(4*8.85),37 + 4.58,'E','W'),
		# linha 2
		array(0,1					,37 + (4.58 * 2),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 2),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 2),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 2),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 2),'E','W'),
		# linha 3
		array(0,1					,37 + (4.58 * 3),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 3),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 3),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 3),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 3),'E','W'),
		# linha 4
		array(0,1					,37 + (4.58 * 4),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 4),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 4),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 4),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 4),'E','W'),
		# linha 5
		array(0,1					,37 + (4.58 * 5),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 5),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 5),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 5),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 5),'E','W'),
		# linha 6
		array(0,1					,37 + (4.58 * 6),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 6),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 6),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 6),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 6),'E','W'),
		# linha 7
		array(0,1					,37 + (4.58 * 7),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 7),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 7),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 7),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 7),'E','W'),
		# linha 8
		array(0,1					,37 + (4.58 * 8),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 8),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 8),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 8),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 8),'E','W'),
		# linha 9
		array(0,1					,37 + (4.58 * 9),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 9),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 9),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 9),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 9),'E','W'),
		# linha 10
		array(0,1					,37 + (4.58 * 10),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 10),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 10),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 10),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 10),'E','W'),
		# linha 11
		array(0,1					,37 + (4.58 * 11),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 11),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 11),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 11),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 11),'E','W'),
		# linha 12
		array(0,1					,37 + (4.58 * 12),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 12),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 12),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 12),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 12),'E','W'),
		# linha 13
		array(0,1					,37 + (4.58 * 13),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 13),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 13),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 13),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 13),'E','W'),
		# linha 14
		array(0,1					,37 + (4.58 * 14),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 14),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 14),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 14),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 14),'E','W'),
		# linha 15
		array(0,1					,37 + (4.58 * 15),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 15),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 15),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 15),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 15),'E','W'),
		# linha 16
		array(0,1					,37 + (4.58 * 16),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 16),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 16),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 16),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 16),'E','W'),
		# linha 17
		array(0,1					,37 + (4.58 * 17),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 17),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 17),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 17),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 17),'E','W'),
		# linha 18
		array(0,1					,37 + (4.58 * 18),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 18),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 18),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 18),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 18),'E','W'),
		# linha 19
		array(0,1					,37 + (4.58 * 19),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 19),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 19),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 19),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 19),'E','W'),
		# linha 20
		array(0,1					,37 + (4.58 * 20),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 20),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 20),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 20),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 20),'E','W'),
		# linha 21
		array(0,1					,37 + (4.58 * 21),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 21),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 21),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 21),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 21),'E','W'),
		# linha 22
		array(0,1					,37 + (4.58 * 22),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 22),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 22),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 22),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 22),'E','W'),
		# linha 23
		array(0,1					,37 + (4.58 * 23),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 23),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 23),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 23),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 23),'E','W'),
		# linha 24
		array(0,1					,37 + (4.58 * 24),'A','W'),
		array(0,1+8.85		,37 + (4.58 * 24),'B','W'),
		array(0,1+(2*8.85),37 + (4.58 * 24),'C','W'),
		array(0,1+(3*8.85),37 + (4.58 * 24),'D','W'),
		array(0,1+(4*8.85),37 + (4.58 * 24),'E','W'),
	),
);