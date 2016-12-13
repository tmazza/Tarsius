<?php
$this->menu=array(
	['Cancelar', $this->createUrl('/configuracao/view', ['id'=>$model->id])],
);
?>

<h2>Atualizar configuração</h2>
<hr><br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>