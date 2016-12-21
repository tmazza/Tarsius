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
		    <?= $form->labelEx($model,'perfil_id',['class'=>'uk-form-label']); ?>

		    <div class="uk-form-controls">
			    <?= $form->dropDownList($model,'perfil_id', $perfis,[
			    	'id'=>'taxPre',
			    	'prompt' => 'Selecione...',
			    ]); 
			    ?>
			    <?php if(count($perfis) > 0): ?>
				    <?=CHtml::link('Ver configurações disponíveis', $this->createUrl('/perfil/index'),[
				    	'target' => '_blank',
				    ]);?>
				     | 
				<?php endif; ?>
			    <?=CHtml::link('Criar nova configuração', $this->createUrl('/perfil/create'),[
			    	'target' => '_blank',
			    ]);?>

			    <br>
			    <small>Caso nenhum perfil de configuração seja utilizado, os valores default de processmento serão aplicados.</small>

			    <?= $form->error($model,'perfil_id'); ?>
			    <br>
			</div>
		</div>
	</fieldset>

	<br><br>

	<fieldset>
		<legend>Exportação dos resultados</legend>

		<div class="uk-form-row">

		    <?= $form->labelEx($model,'export',['class'=>'uk-form-label']); ?>

		    <div class="uk-form-controls">
		    	<?php $count = 0; ?>
		    	<table id='export' class="uk-table uk-table-condensed">
		    		<tr>
		    			<th>Coluna da tabela de exportação</th>
		    			<th>Região no template</th>
		    			<th><a href='#!' onclick="addField()" class="uk-button uk-button-primary"><b>+</b></a></th>
		    		</tr>

		    		<?php foreach ($model->export as $key => $value): ?>
		    			<tr>
		    				<td><input name="export[<?=$count?>][a]" value="<?=$key?>"/></td>
		    				<td><input name="export[<?=$count?>][b]" value="<?=$value?>"/></td>
		    				<td><a href="#!" onclick="$(this).parent().parent().remove();" class="uk-button">&#10006;</a></td>
		    			</tr>
		    			<?php $count++; ?>
		    		<?php endforeach; ?>

		    	</table>
		    	<hr>

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

<script>
var count = <?=$count?>;
function addField(){
	var input1 = '<input name="export['+count+'][a]" />';
	var input2 = '<input name="export['+count+'][b]" />';
	var html = '';
	html += '<tr><td>'+input1+'</td><td>'+input2+'</td><td><a href="#!" onclick="$(this).parent().parent().remove();" class="uk-button">&#10006;</a></td></tr>';

	$('#export').append(html);

	count++;
}
</script>
