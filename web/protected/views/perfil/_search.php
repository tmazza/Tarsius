<?php
/* @var $this TrabalhoPerfilController */
/* @var $model TrabalhoPerfil */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'descricao'); ?>
		<?php echo $form->textArea($model,'descricao',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'enableDebug'); ?>
		<?php echo $form->textField($model,'enableDebug'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'threshold'); ?>
		<?php echo $form->textField($model,'threshold'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minArea'); ?>
		<?php echo $form->textField($model,'minArea'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'maxArea'); ?>
		<?php echo $form->textField($model,'maxArea'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'areaTolerance'); ?>
		<?php echo $form->textField($model,'areaTolerance'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minMatchObject'); ?>
		<?php echo $form->textField($model,'minMatchObject'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'maxExpansions'); ?>
		<?php echo $form->textField($model,'maxExpansions'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'expasionRate'); ?>
		<?php echo $form->textField($model,'expasionRate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'searchArea'); ?>
		<?php echo $form->textField($model,'searchArea'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minMatchEllipse'); ?>
		<?php echo $form->textField($model,'minMatchEllipse'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'templateValidationTolerance'); ?>
		<?php echo $form->textField($model,'templateValidationTolerance'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dynamicPointReference'); ?>
		<?php echo $form->textField($model,'dynamicPointReference'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->