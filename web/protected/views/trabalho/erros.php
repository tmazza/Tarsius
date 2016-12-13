<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/ver',[
		'id' => $id,
	])],
];
?>
<h2>Erros no trabalho</h2>
<?php foreach ($erros as $e): ?>
	<?=CHtml::link('Excluir',$this->createUrl('/trabalho/DeleteErro',[
		'id' => $e->id,
	]));?>
    <pre><?=$e->texto;?></pre><hr>
	<pre><?=$e->trace;?></pre><br><hr><br>
<?php endforeach; ?>

