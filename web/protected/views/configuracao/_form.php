<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'configuracao-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => [
		'class' => 'uk-form uk-form-horizontal'
	],
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="uk-form-row">
		<?php echo $form->labelEx($model,'descricao', ['class' => 'uk-form-label']); ?>
		<?php echo $form->textField($model,'descricao',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'descricao'); ?>
	</div>

	<div class="uk-form-row">
		<?php echo $form->labelEx($model,'maxProcessosAtivos', ['class' => 'uk-form-label']); ?>
		<?php echo $form->textField($model,'maxProcessosAtivos'); ?>
		<?php echo $form->error($model,'maxProcessosAtivos'); ?>
	</div>

	<div class="uk-form-row">
		<?php echo $form->labelEx($model,'maxAquivosProcessos', ['class' => 'uk-form-label']); ?>
		<?php echo $form->textField($model,'maxAquivosProcessos'); ?>
		<?php echo $form->error($model,'maxAquivosProcessos'); ?>
	</div>

	<br>
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',[
		'class' => 'uk-button uk-button-primary'
	]); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->