<?php
$this->menu = [
	['Cancelar',$this->createUrl('/trabalho/index')],
];
?>
<h2>Trabalho</h2>
<div class="uk-form">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
	    'id'=>'trabalho-_ad-form',
	    'enableAjaxValidation'=>false,
	));
	?>

	<?php echo $form->errorSummary($model); ?>

	<div class="uk-row">
	    <?php echo $form->labelEx($model,'nome'); ?>
	    <?php echo $form->textField($model,'nome'); ?>
	    <?php echo $form->error($model,'nome'); ?>
	</div>

	<div class="uk-row">
	    <?php echo $form->labelEx($model,'sourceDir'); ?>
	    <?php echo $form->textField($model,'sourceDir'); ?>
	    <?php echo $form->error($model,'sourceDir'); ?>
	</div>

	<div class="uk-row">
	    <?php echo $form->labelEx($model,'tempoDistribuicao'); ?>
	    <?php echo $form->textField($model,'tempoDistribuicao'); ?>
	    <?php echo $form->error($model,'tempoDistribuicao'); ?>
	</div>

	<div class="uk-row">
	    <?php echo $form->labelEx($model,'template'); ?>
	    <?php echo $form->dropDownList($model,'template',$templates,[
	    	'prompt'=>'Selecione ...',
	    ]); ?>
	    <?php echo $form->error($model,'template'); ?>
	</div>

	<div class="row buttons">
	    <?php echo CHtml::submitButton('Gravar'); ?>
	</div>

</div>

<?php $this->endWidget(); ?>
