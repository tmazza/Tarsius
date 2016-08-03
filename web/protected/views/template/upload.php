<?php if(count($files) > 0): ?>
	<h3>Imagem disponível para geração do template:</h3>
	<?php foreach ($files as $f): ?>
		<?=CHtml::image(Yii::app()->baseUrl . '/../data/gerarTemplate/a.jpg','',[
			'width'=>'400px',
		])?>
		<?=CHtml::link("Criar tempate usando esse arquivo",$this->createUrl('/template/gerar'),[
			'class'=>'uk-button uk-button-primary',
		])?>
	<?php endforeach; ?>
<?php else: ?>
	Nenhum arquivo com formato '.jpg'.
<?php endif; ?>

<hr>
<h3>Novo arquivo para geração de template</h3>
<div class="form">
	<form enctype="multipart/form-data" method="POST">
	    <?= CHtml::fileField('file', null,array('size' => 36, 'maxlength' => 255)); ?>
	    <button type="submit">Salvar</button>
    </form>
</div>