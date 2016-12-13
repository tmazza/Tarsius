<?php
$this->menu=array(
	['Cancelar', $this->createUrl('/configuracao/index')],
);
?>

<h2>Atualizar configuração</h2>
<hr><br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>