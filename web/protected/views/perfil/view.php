<?php
$this->menu=array(
	['Voltar para lista',   $this->createUrl('/perfil/index')],
);
?>

<h2>Perfil <?php echo $model->descricao; ?></h2>
<hr>

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
