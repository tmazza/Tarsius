<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/ver',['id'=>$trabalho->id])],
];
?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<?php
foreach ($trabalho->distribuidos as $d) {
	echo $d->nome . ' | ';
	echo $d->status . ' | ';
	echo str_pad($d->dataFechamento-$d->dataDistribuicao, 2,"0",STR_PAD_LEFT) . 's | ';
	echo CHtml::link('Ver',$this->createUrl('/distribuido/ver',['id'=>$d->id])) . '<br>';
}
?>