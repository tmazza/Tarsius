<?php
$this->menu=array(
    ['Novo perfil', $this->createUrl('/perfil/create')],
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
