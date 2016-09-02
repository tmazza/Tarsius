<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/ver',['id'=>$trabalho->id])],
];
?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$trabalho->getFinalizados(),
    'columns'=>[
    	'nome',
    	[
    		'type'=>'raw',
    		'name'=>'',
    		'value'=>'CHtml::link("Ver",'
    				. 'Yii::app()->controller->createUrl("/distribuido/ver",['
    				. '"id"=>$data->id]))'	
    	],	
    ],
));
?>
