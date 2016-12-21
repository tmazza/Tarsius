<?php
$this->menu=array(
	['Voltar para lista', $this->createUrl('/configuracao/index')],
);
?>

<h2>Perfil <?= $model->descricao; ?></h2>
<hr><br>

<?php 
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'ativo',
		'descricao',
		'maxProcessosAtivos',
        'exportType',
        'exportHost',
        'exportDatabase',
        'exportPort',
        'exportTable',
        'exportUser',
	),
));
?>
