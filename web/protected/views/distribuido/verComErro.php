<?php if($model->status == Distribuido::StatusReprocessamento): ?>
	<meta http-equiv="refresh" content="2">
	<h3><?=$model->nome?></h3>
	<hr>
	<br>
	Aguardando reprocessamento. <small>A págia irá atualizar em 2 segundos...</small>
<?php else: ?>
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
<?php endif; ?>