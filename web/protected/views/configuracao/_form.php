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
		<br>
		<small>
		Quantidade máxima de processo que estarão ativos ao mesmo tempo.
		</small>
	</div>

	<div class="uk-form-row">
		<?php echo $form->labelEx($model,'maxAquivosProcessos', ['class' => 'uk-form-label']); ?>
		<?php echo $form->textField($model,'maxAquivosProcessos'); ?>
		<?php echo $form->error($model,'maxAquivosProcessos'); ?>
		<br><br>
		<small>
		Quantidade máxima de arquivos atribuídas para um processo. Um novo processo só é criado
		quando existem novos imagens no diretório do trabalho e a quantidade de processos ativos
		for menor do que o limite definido.
		</small>
	</div>

	<br>
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',[
		'class' => 'uk-button uk-button-primary'
	]); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->