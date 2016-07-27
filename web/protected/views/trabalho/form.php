<?php
$this->menu = [
	['Cancelar',$this->createUrl('/trabalho/index')],
];
?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; Novo trabalho
</h3>
<div class="uk-form uk-form-horizontal">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
	    'id'=>'trabalho-_ad-form',
	    'enableAjaxValidation'=>false,
	));
	?>

	<?php echo $form->errorSummary($model); ?>

	<div class="uk-form-row">
	    <?php echo $form->labelEx($model,'nome',['class'=>'uk-form-label']); ?>
	    <div class="uk-form-controls">
		    <?php echo $form->textField($model,'nome',['class'=>'uk-width-1-1']); ?>
		    <?php echo $form->error($model,'nome'); ?>
	    </div>
	</div>

	<div class="uk-form-row">
	    <?php echo $form->labelEx($model,'sourceDir',['class'=>'uk-form-label']); ?>
	    <div class="uk-form-controls">
		    <?php echo $form->textField($model,'sourceDir',['class'=>'uk-width-1-1']); ?>
		    <?php echo $form->error($model,'sourceDir'); ?>
	    </div>
	</div>

	<div class="uk-form-row">
	    <?php echo $form->labelEx($model,'tempoDistribuicao',['class'=>'uk-form-label']); ?>
	    <div class="uk-form-controls">
		    <?php echo $form->textField($model,'tempoDistribuicao'); ?>
		    <?php echo $form->error($model,'tempoDistribuicao'); ?>
		</div>
	</div>

	<div class="uk-form-row">
	    <?php echo $form->labelEx($model,'template',['class'=>'uk-form-label']); ?>
	    <div class="uk-form-controls">
	    	<?php echo $form->dropDownList($model,'template',$templates,[
		    	'prompt'=>'Selecione ...',
		    ]); ?>
		    <?php echo $form->error($model,'template'); ?>
		</div>
	</div>
	<div class="uk-form-row">
	    <?php echo $form->labelEx($model,'taxaPreenchimento',['class'=>'uk-form-label']); ?>

	    <div class="uk-form-controls">
		    <?php echo $form->textField($model,'taxaPreenchimento',[
		    	'id'=>'taxPre',
		    ]); 

		    ?>
		    <?php echo $form->error($model,'taxaPreenchimento'); ?>
		</div>
	</div>
	<br>
	<div class="uk-row">
	    <?php echo CHtml::submitButton('Gravar',['class'=>'uk-button']); ?>
	</div>

</div>

<?php $this->endWidget(); ?>
