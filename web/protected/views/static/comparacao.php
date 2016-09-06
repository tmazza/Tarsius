<h3>Iguais | <?=count($iguais);?></h3>
<h3>Diferentes | <?=count($diferencas);?></h3>
<pre>
	<?php
	foreach ($diferencas as $f => $d) {
		echo CHtml::link($f,$url.$f.'.jpg') . '|';
		$model = $d['model'];	
		echo CHtml::link($f,Yii::app()->params['urlBase'].'/concurso/tarsius2/web/index.php/distribuido/ver/id/'.$model->id) . '|';
		foreach ($d['diferencas'] as $diff) {
			$pos = $diff['posicao'];
			$trab = $diff['local'];
			$conc = $diff['export'];
			$alt = $pos % 5;
			$qst = ($pos - $alt) / 5 + 1;
			echo "($qst,$alt: $trab - $conc)";
		}
		echo "<hr>";
	}
	?>
</pre>
<br>
<h3>Não encontradas no trabalho | <?=count($naoEncontradas);?></h3>
<pre>
	<?php
	print_r($naoEncontradas);
	?>
</pre>

<br>
<h3>Resposta não definida no trabalho | possível erro de âncora | <?=count($respNaoDefinida);?></h3>
<pre>
	<?php
	print_r($respNaoDefinida);
	?>
</pre>
