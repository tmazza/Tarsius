<?php
$this->menu = [
	['Novo trabalho',$this->createUrl('/trabalho/novo')],
];
?>
<h2>Trabalhos</h2>
<?php foreach($trabalhos as $t): ?>
	<h3>
		<?=CHtml::link($t->nome,$this->createUrl('/trabalho/ver',[
			'id'=>$t->id,
		]));?>		
	</h3>
	<?=CHtml::link('Configurar',$this->createUrl('/trabalho/editar',['id'=>$t->id,]));?>
	<br>

	<?=$t->sourceDir?><br>
	<?=$t->tempoDistribuicao?> seg
	<hr>
<?php endforeach; ?>
