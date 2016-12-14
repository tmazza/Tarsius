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


	<br><br>
	<div class="uk-margin-large-left">
		<fieldset>
			<legend>Exportação dos resultados</legend>
			
			<div class="uk-form-row">
				<?php echo $form->labelEx($model,'exportType', ['class' => 'uk-form-label']); ?>
				<?php echo $form->dropDownList($model,'exportType', Configuracao::getTipos(), [
					'onchange' => 'hideShowExport()',
					'id' => 'exportType',
				]); ?>
				<?php echo $form->error($model,'exportType'); ?>
				<br>
				<small>
				<ul>
					<li>Desabilitada: os arquivos processados ficarão disponíveis como "Processados", sem possibilidade de exportá-los</li>
					<li>Pendente: os arquivos processados ficarão disponíveis como "Não exportados" e será possível exportá-los em outro momento.</li>
					<li>Outros: os arquivos processados seão exportados para o banco de dados definido. Caso algum erro ocorra durante a exportação ou a configuração seja inválida esse arquivos ficarão no estado "Não exportados" e poderão ser manualmente exportado.</li>
				</ul>
				</small>
			</div>	

			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportHost', ['class' => 'uk-form-label']); ?>
				<?php echo $form->textField($model,'exportHost'); ?>
				<?php echo $form->error($model,'exportHost'); ?>
				<br>
				<small>
				
				</small>
			</div>
			
			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportDatabase', ['class' => 'uk-form-label']); ?>
				<?php echo $form->textField($model,'exportDatabase'); ?>
				<?php echo $form->error($model,'exportDatabase'); ?>
				<br>
				<small>
				
				</small>
			</div>
			
			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportPort', ['class' => 'uk-form-label']); ?>
				<?php echo $form->textField($model,'exportPort'); ?>
				<?php echo $form->error($model,'exportPort'); ?>
				<br>
				<small>
				
				</small>
			</div>
			
			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportUser', ['class' => 'uk-form-label']); ?>
				<?php echo $form->textField($model,'exportUser'); ?>
				<?php echo $form->error($model,'exportUser'); ?>
				<br>
				<small>
				
				</small>
			</div>
			
			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportPwd', ['class' => 'uk-form-label']); ?>
				<?php echo $form->passwordField($model,'exportPwd'); ?>
				<?php echo $form->error($model,'exportPwd'); ?>
				<br>
				<small>
				
				</small>
			</div>

			<div class="uk-form-row toExport">
				<?php echo $form->labelEx($model,'exportTable', ['class' => 'uk-form-label']); ?>
				<?php echo $form->textField($model,'exportTable'); ?>
				<?php echo $form->error($model,'exportTable'); ?>
				<br>
				<small>
				
				</small>
			</div>

		</fieldset>
	</div>

	<br><br><br>
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',[
		'class' => 'uk-button uk-button-primary'
	]); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
	
	$('#exportType').change(function() {
		hideShowExport();
	});

	function hideShowExport() {
		var val = $('#exportType').val();
		if (val == 0 || val == 1) {
			$('.toExport').hide();
		} else {
			$('.toExport').show();
		}
	}
	hideShowExport();
});

</script>