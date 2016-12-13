<tr>
	<td><h3><?= CHtml::encode($data->descricao); ?></h3></td>
    <td style='width:80px'><?= CHtml::link('Ver <i class="uk-icon uk-icon-eye"></i>', array('view', 'id'=>$data->id),[
        'class' => 'uk-button'
    ]); ?></td>
    <td style='width:80px'><?= CHtml::link('Editar <i class="uk-icon uk-icon-edit"></i>', array('update', 'id'=>$data->id),[
        'class' => 'uk-button'
    ]); ?></td>
    <td style='width:100px'><?= CHtml::link('Excluir <i class="uk-icon uk-icon-trash"></i>', array('delete', 'id'=>$data->id),[
        'class' => 'uk-button',
        'confirm' => 'Confirma exclusão?',
    ]); ?></td>
</tr>
