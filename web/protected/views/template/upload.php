<?php
$this->menu = [
	['Cancelar',$this->createUrl('/template/index')],
];
?>
<hr>
<h3>Geração de template</h3>
<div class="form">
	<form enctype="multipart/form-data" method="POST" class="uk-form">
		<h4>Nome:</h4>		
	   	<input name="nome" value="<?=$model->nome?>" class="" style="width:200px;" />
	   	<small>Sem espaços, acentos ou caracteres especiais.</small>
	   	<br>
	   	<h4>Imagem de base:</h4>
	    <?= CHtml::fileField('file', null,array('size' => 36, 'maxlength' => 255)); ?>
	   	<br><br>
	   	<br><br>
	    <button type="submit" class="uk-button uk-button-primary">
	    	Salvar
	    </button>
    </form>
</div>