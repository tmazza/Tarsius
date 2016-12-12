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

	<?= $form->errorSummary($model); ?>

	<fieldset>
		<legend>Informações básicas</legend>

		<div class="uk-form-row">
		    <?= $form->labelEx($model,'nome',['class'=>'uk-form-label']); ?>
		    <div class="uk-form-controls">
			    <?= $form->textField($model,'nome',['class'=>'uk-width-1-1']); ?>
			    <?= $form->error($model,'nome'); ?>
			    <small>Use somente letras, números e espaços.</small>
		    </div>
		</div>

		<div class="uk-form-row">
		    <?= $form->labelEx($model,'template',['class'=>'uk-form-label']); ?>
		    <div class="uk-form-controls">
		    	<?= $form->dropDownList($model,'template',$templates,[
			    	'prompt'=>'Selecione ...',
			    ]); ?>
			    <?= $form->error($model,'template'); ?>
			   	<br>
			    <small>
			    Caso não tenha criado o template ainda deixe este campo em branco e salve o trabalho, depois acesse a aba "Templates" para criá-lo.</small>
			</div>
		</div>
	</fieldset>
	<br><br>

	<fieldset>
		<legend>Distribuição das imagens</legend>

		<div class="uk-form-row">
		    <?= $form->labelEx($model,'sourceDir',['class'=>'uk-form-label']); ?>
		    <div class="uk-form-controls">
			    <?= $form->textField($model,'sourceDir',['class'=>'uk-width-1-1']); ?>
			    <?= $form->error($model,'sourceDir'); ?>
			    <small>Diretório que contem as imagens que devem ser interpretadas. 
			    Inclua a última '/' no final do caminho.
			    </small>
		    </div>
		</div>
		
		<div class="uk-form-row">
		    <?= $form->labelEx($model,'urlImagens',['class'=>'uk-form-label']); ?>
		    <div class="uk-form-controls">
			    <?= $form->textField($model,'urlImagens',['class'=>'uk-width-1-1']); ?>
			    <?= $form->error($model,'urlImagens'); ?>
			    <small>
			    	Link, acessível pelo <i>browser</i>, para o diretório que contém as imagens. O link deve
			    	começar com http ou https.
			    </small>
		    </div>
		</div>

		<div class="uk-form-row">
		    <?= $form->labelEx($model,'tempoDistribuicao',['class'=>'uk-form-label']); ?>
		    <div class="uk-form-controls">
			    <?= $form->textField($model,'tempoDistribuicao'); ?>
			    <?= $form->error($model,'tempoDistribuicao'); ?>
			    <br>
			    <small>Intervalo de tempo (em segundos) entre duas distribuições. Cada distribuição é a busca de um conjunto de imagens do diretório de trabalho.</small>
			</div>
		</div>
	</fieldset>
	<br><br>
	<fieldset>
		<legend>Processamento</legend>
		<div class="uk-form-row">
		    <?= $form->labelEx($model,'taxaPreenchimento',['class'=>'uk-form-label']); ?>

		    <div class="uk-form-controls">
			    <?= $form->textField($model,'taxaPreenchimento',[
			    	'id'=>'taxPre',
			    ]); 
			    ?>
			    <?= $form->error($model,'taxaPreenchimento'); ?>
			    <br>
			    <small>
			    	Taxa de preenchimento mínimo (de 0 a 1) para considerar um elipse como marcada.
			    	Use ponto para definir a taxa de preenchimento, por exemplo, para 30% informe 0.3

			    </small>
			</div>
		</div>
	</fieldset>

	<br><br>

	<fieldset>
		<legend>Exportação dos resultados</legend>
		<div class="uk-form-row">
		    <?= $form->labelEx($model,'export',['class'=>'uk-form-label']); ?>

		    <div class="uk-form-controls">
			    <?= $form->textArea($model,'export',[
			    	'id'=>'taxPre',
			    	'style'=>'width:100%;min-height:200px;',
			    ]); 

			    ?>
			    <?= $form->error($model,'export'); ?>
			</div>
		</div>
	</fieldset>
	<br>
	<div class="uk-row">
	    <?= CHtml::submitButton('Gravar',['class'=>'uk-button']); ?>
	</div>

</div>

<?php $this->endWidget(); ?>
