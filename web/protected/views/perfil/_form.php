
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'trabalho-perfil-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'uk-form uk-form-horizontal']
)); ?>

	<?= $form->errorSummary($model); ?>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'descricao',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'descricao',array('rows'=>6, 'cols'=>50)); ?>
		<?= $form->error($model,'descricao'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'enableDebug',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'enableDebug'); ?>
		<?= $form->error($model,'enableDebug'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'threshold',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'threshold'); ?>
		<?= $form->error($model,'threshold'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'minArea',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'minArea'); ?>
		<?= $form->error($model,'minArea'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'maxArea',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'maxArea'); ?>
		<?= $form->error($model,'maxArea'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'areaTolerance',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'areaTolerance'); ?>
		<?= $form->error($model,'areaTolerance'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'minMatchObject',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'minMatchObject'); ?>
		<?= $form->error($model,'minMatchObject'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'maxExpansions',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'maxExpansions'); ?>
		<?= $form->error($model,'maxExpansions'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'expasionRate',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'expasionRate'); ?>
		<?= $form->error($model,'expasionRate'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'searchArea',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'searchArea'); ?>
		<?= $form->error($model,'searchArea'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'minMatchEllipse',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'minMatchEllipse'); ?>
		<?= $form->error($model,'minMatchEllipse'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'templateValidationTolerance',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'templateValidationTolerance'); ?>
		<?= $form->error($model,'templateValidationTolerance'); ?>
	</div>

	<div class="uk-form-row">
		<?= $form->labelEx($model,'dynamicPointReference',['class' => 'uk-form-label']); ?>
		<?= $form->textField($model,'dynamicPointReference'); ?>
		<?= $form->error($model,'dynamicPointReference'); ?>
	</div>
	<br><br>
	<?= CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',[
		'class' => 'uk-button uk-button-primary',
	]); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->