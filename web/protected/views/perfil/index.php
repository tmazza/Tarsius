<?php
$this->menu=array(
    ['Nova configuração', $this->createUrl('/perfil/create')],
);
?>

<h2>Configurações de processamento</h2>
<hr>

<table class="uk-table">
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'_view',
    )); ?>
</table>
