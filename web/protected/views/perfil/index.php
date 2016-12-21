<?php
$this->menu=array(
    ['Nova configuração', $this->createUrl('/perfil/create')],
    ['Voltar para lista de trabalhos', $this->createUrl('/trabalho/index')],
);
?>
<h2>Perfis de configuração de processamento</h2>
<hr>

<table class="uk-table">
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'_view',
    )); ?>
</table>
