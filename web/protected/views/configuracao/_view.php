<tr>
	<td>
		<h3>
			<?= CHtml::encode($data->descricao); ?>
			<?php if($data->ativo): ?>
				<div class="uk-badge uk-badge-success">ativo</div>			
			<?php endif; ?>
		</h3>
	</td>
    <td style='width: 300px'>
    <?= CHtml::link('Ativar', array('makeActive', 'id'=>$data->id),[
        'class' => 'uk-button'
    ]); ?>
    <?= CHtml::link('Ver <i class="uk-icon uk-icon-eye"></i>', array('view', 'id'=>$data->id),[
        'class' => 'uk-button'
    ]); ?>
        
    <?= CHtml::link('Editar <i class="uk-icon uk-icon-edit"></i>', array('update', 'id'=>$data->id),[
        'class' => 'uk-button'
    ]); ?>
        
    <?= CHtml::link('Excluir <i class="uk-icon uk-icon-trash"></i>', array('delete', 'id'=>$data->id),[
        'class' => 'uk-button',
        'confirm' => 'Confirma exclusão?',
    ]); ?>
    </td>
</tr>
