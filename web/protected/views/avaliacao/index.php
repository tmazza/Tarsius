<?php
$this->menu = [];
?>
<h2>Avaliações de desempenho</h2>
<hr>
<?php if(count($files) > 0): ?>
	<?php foreach($files as $f): ?>
		<?=CHtml::link(basename($f),Yii::app()->baseUrl.'/../data/comparacoes/'.basename($f),[
			'target'=>'_blank',
		]);?><br>
	<?php endforeach; ?>
<?php else: ?>
	<div class="uk-alert">
		Nenhuma comparação disponível.		
	</div>
<?php endif; ?>