<?php

$this->menu=array(
	array('label'=>'List TrabalhoPerfil', 'url'=>array('index')),
	array('label'=>'Create TrabalhoPerfil', 'url'=>array('create')),
	array('label'=>'View TrabalhoPerfil', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage TrabalhoPerfil', 'url'=>array('admin')),
);
?>

<h1>Update TrabalhoPerfil <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>