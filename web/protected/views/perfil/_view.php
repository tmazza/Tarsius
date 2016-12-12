<tr>
	<td><h3><?= CHtml::encode($data->descricao); ?></h3></td>
	<td><?= CHtml::link('Ver mais', array('view', 'id'=>$data->id),[
		'class' => 'uk-button'
	]); ?></td>
</tr>
