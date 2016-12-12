<?php
$this->menu=array(
    ['Cancelar',   $this->createUrl('/perfil/index')],
);
?>
<br>
<h2>Nova congiguração de processamento</h2>
<hr>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>