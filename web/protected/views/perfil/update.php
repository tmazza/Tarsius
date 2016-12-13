<?php
$this->menu=array(
    ['Cancelar',   $this->createUrl('/perfil/index')],
);
?>
<h2>Update TrabalhoPerfil <?php echo $model->id; ?></h2>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>