<div id='content-view'>
	<?php if($msg): ?>
	<?=$msg;?>
<?php endif; ?>
<?=CHtml::beginForm();?>
	<?=CHtml::textField('minMatch',0.85);?>
	<br>
	Validar número do template: <?=CHtml::checkbox('validaTemplate',true);?>
	<?=CHtml::submitButton('Ver resultado');?>
<?=CHtml::endForm();?>	
</div>