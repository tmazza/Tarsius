<div id='content-view'>
	<?php if($msg): ?>
	<?=$msg;?>
<?php endif; ?>
<?=CHtml::beginForm();?>
	<?=CHtml::textField('minMatch',0.85);?>
	<?=CHtml::submitButton('Ver resultado');?>
<?=CHtml::endForm();?>	
</div>