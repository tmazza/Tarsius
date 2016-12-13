<?php
$this->menu=[
	['Cancelar', $this->createUrl('/configuracao/index')],
];
?>

<h2>Nova configuração</h2>
<hr><br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>