<?php
$this->menu=array(
    ['Cancelar',   $this->createUrl('/perfil/index')],
);
?>
<h2>Nova configuração de processamento</h2>
<hr>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>