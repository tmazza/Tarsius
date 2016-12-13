<?php
$this->menu=[
	['Novo perfil',$this->createUrl('/configuracao/create')],
];
?>

<h2>Perfis de configuração</h2>
<hr><br>

<table class="uk-table">
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'_view',
        'emptyText'=>'<div class="uk-alert">Nenhum perfil encotrado.</div>',
    )); ?>
</table>
