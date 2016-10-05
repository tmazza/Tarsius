<?php if(!is_null($model)): ?>
	<?php
	$this->menu = [
		['Voltar',$this->createUrl('/trabalho/finalizadas',['id'=>$model->trabalho->id])],
	];
	?>
	<h3><?=$model->nome?></h3>
	<hr>
	<br>
<?php else: ?>
	<h3>Não encontrado.</h3>
	<hr>
<?php endif; ?>
<h5><?=$msg?></h5>