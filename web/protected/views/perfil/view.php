<?php
$this->menu=array(
	['Voltar para lista',   $this->createUrl('/perfil/index')],
);
?>

<h1>View TrabalhoPerfil #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'descricao',
		'enableDebug',
		'threshold',
		'minArea',
		'maxArea',
		'areaTolerance',
		'minMatchObject',
		'maxExpansions',
		'expasionRate',
		'searchArea',
		'minMatchEllipse',
		'templateValidationTolerance',
		'dynamicPointReference',
	),
)); ?>
