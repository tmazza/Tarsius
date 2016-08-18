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
    		'name'=>'asd',
    		'value'=>'$data->status == 2 ? CHtml::link("Ver",'
    				. 'Yii::app()->controller->createUrl("/distribuido/ver",['
    				. '"id"=>$data->id])) : "";'	
    	],	
    ],
));
?>
<!-- <td><?//=str_pad($d->dataFechamento-$d->dataDistribuicao, 2,"0",STR_PAD_LEFT) . 's | ';?></td> -->
<!-- echo CHtml::link('Ver',$this->createUrl('/distribuido/ver',['id'=>$d->id])) . '<br>'; -->
